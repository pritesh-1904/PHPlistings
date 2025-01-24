<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2024 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Controllers;

class Cron
    extends \App\Src\Mvc\BaseController
{

    public function actionIndex($params)
    {
        locale()->setLocale(locale()->getDefault());

        return earlyResponse()
            ->setHeader('Content-Type', 'image/gif')
            ->setContent(base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'))
            ->setCallback([$this, 'cron']);
    }

    public function cron()
    {
        foreach((new \App\Models\Cronjob())->getScheduledJobs() as $job) {
            if ($job->locked == 1) {
                continue;
            }

            $lastRunDatetime = $job->last_run_datetime;

            $job->locked = 1;
            $job->last_run_datetime = date('Y-m-d H:i:s');
            $job->save();

            $job->response = $this->{$job->name}($lastRunDatetime);
            $job->locked = null;
            $job->save();
        }
    }

    public function daily()
    {
        \App\Models\Reminder::query()
            ->where(db()->raw('DATEDIFF(CURDATE(), DATE(added_datetime)) >= ?', [1]))
            ->delete();

        \App\Models\Email::query()
            ->where('status', 'sent')
            ->where(db()->raw('DATEDIFF(CURDATE(), DATE(added_datetime)) >= ?', [30]))
            ->delete();

        \App\Models\RawStat::query()
            ->where(db()->raw('DATEDIFF(CURDATE(), DATE(added_date)) >= ?', [7]))
            ->delete();
    }

    public function hourly()
    {
        db()->table('cache')
            ->where('ctimestamp', '<', time())
            ->delete();
    }

    public function everyminute()
    {
        $type = \App\Models\Type::whereNotNull('deleted')->first();

        if (null !== $type) {
            $listings = $type->listings()->limit(50)->get();
            if ($listings->count() > 0) {
                foreach ($listings as $listing) {
                    $listing->delete();
                }
            } else {
                foreach (config()->themes->pages->type as $key => $value) {
                    $page = \App\Models\Page::query()
                        ->where('slug', 'type/' . $key)
                        ->where('type_id', $type->id)
                        ->first();

                    if (null !== $page) {
                        $page->delete();
                    }
                }

                $type->parents()->detach();
                $type->children()->detach();
                $type->ratings()->detach();

                foreach ($type->fields as $field) {
                    $field->delete();
                }

                if (null !== $root = (new \App\Models\Category)->getRoot($type->id)) {
                    $root->delete();
                }

                foreach ($type->products as $product) {
                    $product->delete();
                }

                foreach ($type->badges as $badge) {
                    $badge->delete();
                }

                $type->unsort();

                $type->delete();
            }
        }

        // Synchronize
        
        $qb = \App\Models\Listing::query()
            ->whereNotNull('sync_product')
            ->with('order.pricing.product')
            ->limit(50);

        foreach ($qb->get() as $listing) {
            $listing->synchronizeProduct();
            $listing->save();
        }    
    
        $qb = \App\Models\Order::query()
            ->whereNotNull('sync_pricing')
            ->with('pricing')
            ->limit(50);

        foreach ($qb->get() as $order) {
            $order->synchronizePricing($order->pricing);
            $order->save();
        }

        // Activate pending or suspended orders with paid invoice

        $qb = \App\Models\Order::query()
            ->where(function ($query) {
                $query
                    ->where('status', 'pending')
                    ->orWhere('status', 'suspended');
            })
            ->whereHas('invoice', function ($relation) {
                $relation
                    ->whereNull('end_datetime')
                    ->where('status', 'paid');
            })
            ->limit(50);

        foreach ($qb->get() as $order) {            
            $start = date('Y-m-d H:i:s');
            $end = $order->invoice->getBillingCycleEndDatetime();

            $order->status = 'active';
            $order->start_datetime = $start;
            $order->end_datetime = $end;
            $order->notification_1_sent = null;
            $order->notification_2_sent = null;
            $order->notification_3_sent = null;
            $order->save();

            $order->invoice->start_datetime = $start;
            $order->invoice->end_datetime = $end;
            $order->invoice->save();

            $order->listing->status = 'active';
            $order->listing->save();                    
        }
    }

    public function deadlinkchecker()
    {        
        if (null !== config()->other->deadlinkchecker) {
            $qb = \App\Models\Listing::query()
                ->whereNotNull('active')
                ->where('status', 'active')
                ->where( function ($query) {
                    $query
                        ->where( function ($query) {
                            $query
                                ->where( function ($query) {
                                    $query
                                        ->whereNull('deadlinkchecker_datetime')
                                        ->orWhere(db()->raw('DATEDIFF(CURDATE(), DATE(deadlinkchecker_datetime)) >= ?', [config()->other->deadlinkchecker_interval]));
                                })
                                ->whereNull('deadlinkchecker_retry');
                        })
                        ->orWhere( function ($query) {
                            $query
                                ->whereNotNull('deadlinkchecker_retry')
                                ->where('deadlinkchecker_retry', '<=', config()->other->deadlinkchecker_max_retry_count)
                                ->where(db()->raw('TIMESTAMPDIFF(HOUR, deadlinkchecker_datetime, NOW()) >= ?', [config()->other->deadlinkchecker_retry_interval]));
                        });
                })
                ->with('type')
                ->with('user')
                ->with('data', function ($query) {
                    $query->where('field_name', 'website');
                })
                ->limit(25);

            foreach ($qb->get() as $listing) {
                $listing->deadlinkchecker_code = null;

                if (null !== $field = $listing->data->where('field_name', 'website')->first()) {
                    if (null !== $field->get('active') && '' != $field->get('value', '')) {
                        $listing->deadlinkchecker_retry = $listing->get('deadlinkchecker_retry', 0) + 1;

                        $ch = curl_init(d($field->get('value')));
                        curl_setopt($ch, \CURLOPT_HEADER, true);
                        curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, \CURLOPT_FOLLOWLOCATION, true);
                        curl_setopt($ch, \CURLOPT_TIMEOUT, 10);

                        curl_setopt($ch, \CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:104.0) Gecko/20100101 Firefox/104.0');
                        curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8']);
                        curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Accept-Language: en']);
                        curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Accept-Encoding: gzip, deflate, br']);
                        curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Connection: keep-alive']);
                        curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Upgrade-Insecure-Requests: 1']);

                        curl_setopt($ch, \CURLOPT_REFERER, $_SERVER['REQUEST_URI']);

                        curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Sec-Fetch-Dest: document']);
                        curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Sec-Fetch-Mode: navigate']);
                        curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Sec-Fetch-Site: cross-site']);
                        curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Pragma: no-cache']);
                        curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Pragma-Control: no-cache']);

                        curl_setopt($ch, \CURLOPT_MAXREDIRS, 20);
                        curl_setopt($ch, \CURLOPT_SSL_VERIFYHOST, false);
                        curl_setopt($ch, \CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, \CURLOPT_AUTOREFERER, true);
                        curl_setopt($ch, \CURLOPT_SSLVERSION, 6);

                        $output = curl_exec($ch);
                        $responseCode = curl_getinfo($ch, \CURLINFO_RESPONSE_CODE);
                        curl_close($ch);

                        $listing->deadlinkchecker_code = (int) $responseCode;

                        if ($responseCode > 0 && false === in_array($responseCode, ['404', '500'])) {
                            $listing->deadlinkchecker_retry = null;
                        }

                        $emailData = [
                            'id' => $listing->user->id,
                            'first_name' => $listing->user->first_name,
                            'last_name' => $listing->user->last_name,
                            'email' => $listing->user->email,

                            'listing_id' => $listing->id,
                            'listing_title' => $listing->title,
                            'listing_type_singular' => $listing->type->name_singular,
                            'listing_type_plural' => $listing->type->name_plural,

                            'url' => $field->get('value'),
                            'link' => route('account/manage/' . $listing->type->slug . '/summary/' . $listing->slug),
                        ];

                        if (null !== $listing->type->get('active') && '' != config()->other->deadlinkchecker_client_notification_retry && config()->other->deadlinkchecker_client_notification_retry == $listing->deadlinkchecker_retry) {
                            (new \App\Repositories\EmailQueue())->push(
                                'user_invalid_link',
                                $listing->user->id,
                                $emailData,
                                [$listing->user->email => $listing->user->getName()],
                                [config()->email->from_email => config()->email->from_name]
                            );
                        }

                        if ('' != config()->other->deadlinkchecker_admin_notification_retry && config()->other->deadlinkchecker_admin_notification_retry == $listing->deadlinkchecker_retry) {
                            $emailData['link'] = adminRoute('manage/' . $listing->type->slug . '/summary/' . $listing->id);
                            
                            (new \App\Repositories\EmailQueue())->push(
                                'admin_invalid_link',
                                null,
                                $emailData,
                                [config()->email->from_email => config()->email->from_name],
                                [config()->email->from_email => config()->email->from_name]
                            );
                        }

                        if (null !== config()->other->deadlinkchecker_autoremove_failed_link && config()->other->deadlinkchecker_max_retry_count == $listing->deadlinkchecker_retry) {
                            $listing->deadlinkchecker_retry = null;

                            db()->table('listingfielddata')
                                ->where('listing_id', $listing->id)
                                ->where('field_name', 'website')
                                ->update([
                                    'value' => '',
                                ]);
                        }
                    }
                }

                $listing->deadlinkchecker_datetime = date("Y-m-d H:i:s");
                $listing->save();
            }
        }
    }

    public function backlinkchecker()
    {        
        if (null !== config()->other->backlinkchecker) {
            $url = ('' != config()->other->get('backlinkchecker_url', '') ? config()->other->get('backlinkchecker_url') : config()->app->url);
            
            $qb = \App\Models\Listing::query()
                ->whereNotNull('active')
                ->where('status', 'active')
                ->whereNotNull('_backlink')
                ->where( function ($query) {
                    $query
                        ->where( function ($query) {
                            $query
                                ->where( function ($query) {
                                    $query
                                        ->whereNull('backlinkchecker_datetime')
                                        ->orWhere(db()->raw('DATEDIFF(CURDATE(), DATE(backlinkchecker_datetime)) >= ?', [config()->other->backlinkchecker_interval]));
                                })
                                ->whereNull('backlinkchecker_retry');
                        })
                        ->orWhere( function ($query) {
                            $query
                                ->whereNotNull('backlinkchecker_retry')
                                ->where('backlinkchecker_retry', '<=', config()->other->backlinkchecker_max_retry_count)
                                ->where(db()->raw('TIMESTAMPDIFF(HOUR, backlinkchecker_datetime, NOW()) >= ?', [config()->other->backlinkchecker_retry_interval]));
                        });
                })
                ->with('type')
                ->with('user')
                ->with('data', function ($query) {
                    $query->where('field_name', 'website');
                })
                ->limit(25);

            foreach ($qb->get() as $listing) {
                $listing->backlinkchecker_code = null;
                $listing->backlinkchecker_linkrelation = null;

                if (null !== $field = $listing->data->where('field_name', 'website')->first()) {
                    if (null !== $field->get('value') && '' != $field->get('value')) {
                        $listing->backlinkchecker_retry = $listing->get('backlinkchecker_retry', 0) + 1;

                        $ch = curl_init(d($field->get('value')));
                        curl_setopt($ch, \CURLOPT_HEADER, true);
                        curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, \CURLOPT_FOLLOWLOCATION, true);
                        curl_setopt($ch, \CURLOPT_TIMEOUT, 10);

                        curl_setopt($ch, \CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:104.0) Gecko/20100101 Firefox/104.0');
                        curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8']);
                        curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Accept-Language: en']);
                        curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Accept-Encoding: gzip, deflate, br']);
                        curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Connection: keep-alive']);
                        curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Upgrade-Insecure-Requests: 1']);

                        curl_setopt($ch, \CURLOPT_REFERER, $_SERVER['REQUEST_URI']);

                        curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Sec-Fetch-Dest: document']);
                        curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Sec-Fetch-Mode: navigate']);
                        curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Sec-Fetch-Site: cross-site']);
                        curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Pragma: no-cache']);
                        curl_setopt($ch, \CURLOPT_HTTPHEADER, ['Pragma-Control: no-cache']);

                        curl_setopt($ch, \CURLOPT_MAXREDIRS, 20);
                        curl_setopt($ch, \CURLOPT_SSL_VERIFYHOST, false);
                        curl_setopt($ch, \CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, \CURLOPT_AUTOREFERER, true);
                        curl_setopt($ch, \CURLOPT_SSLVERSION, 6);
                        
                        $responseText = curl_exec($ch);
                        $responseCode = curl_getinfo($ch, \CURLINFO_RESPONSE_CODE);
                        curl_close($ch);

                        $listing->backlinkchecker_code = (int) $responseCode;

                        if ('200' == $responseCode && false !== $responseText) {
                            $dom = new \DOMDocument();

                            if (false !== @$dom->loadHTML($responseText)) {
                                $xpath = new \DOMXPath($dom);

                                if (false !== $tags = $xpath->evaluate("/html/body//a")) {
                                    for ($i = 0; $i < $tags->length; $i++) {
                                        $tag = $tags->item($i);

                                        $href = $tag->getAttribute('href');
                                        $rel = $tag->getAttribute('rel');
                                        
                                        if (trim($href, '/ ') == $url) {
                                            $listing->backlinkchecker_linkrelation = (false === stristr($rel, 'nofollow') ? 'dofollow' : 'nofollow');
                                            
                                            if (null === config()->other->backlinkchecker_follow_only || (null !== config()->other->backlinkchecker_follow_only && false === stristr($rel, 'nofollow'))) {
                                                $listing->backlinkchecker_retry = null;
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        $emailData = [
                            'id' => $listing->user->id,
                            'first_name' => $listing->user->first_name,
                            'last_name' => $listing->user->last_name,
                            'email' => $listing->user->email,

                            'listing_id' => $listing->id,
                            'listing_title' => $listing->title,
                            'listing_type_singular' => $listing->type->name_singular,
                            'listing_type_plural' => $listing->type->name_plural,

                            'link' => route('account/manage/' . $listing->type->slug . '/summary/' . $listing->slug),
                        ];

                        if (null !== $listing->type->get('active') && '' != config()->other->backlinkchecker_client_notification_retry && config()->other->backlinkchecker_client_notification_retry == $listing->backlinkchecker_retry) {
                            (new \App\Repositories\EmailQueue())->push(
                                'user_invalid_backlink',
                                $listing->user->id,
                                $emailData,
                                [$listing->user->email => $listing->user->getName()],
                                [config()->email->from_email => config()->email->from_name]
                            );
                        }

                        if ('' != config()->other->backlinkchecker_admin_notification_retry && config()->other->backlinkchecker_admin_notification_retry == $listing->deadlinkchecker_retry) {
                            $emailData['link'] = adminRoute('manage/' . $listing->type->slug . '/summary/' . $listing->id);
                            
                            (new \App\Repositories\EmailQueue())->push(
                                'admin_invalid_backlink',
                                null,
                                $emailData,
                                [config()->email->from_email => config()->email->from_name],
                                [config()->email->from_email => config()->email->from_name]
                            );
                        }

                        if (null !== config()->other->backlinkchecker_cancel_failed_listing && config()->other->backlinkchecker_max_retry_count == $listing->backlinkchecker_retry) {
                            $listing->backlinkchecker_retry = null;

                            $listing->order->deactivate((null !== config()->other->backlinkchecker_cancel_failed_listing_refund ? true : false));

                            (new \App\Repositories\EmailQueue())->push(
                                'user_order_cancelled_invalid_backlink',
                                $listing->user->id,
                                $emailData,
                                [$listing->user->email => $listing->user->getName()],
                                [config()->email->from_email => config()->email->from_name]
                            );
                        }
                    }
                }

                $listing->backlinkchecker_datetime = date("Y-m-d H:i:s");
                $listing->save();
            }
        }
    }

    public function mail()
    {
        return (new \App\Repositories\EmailQueue())->dispatch(config()->email->queue_rate);
    }

    public function statistics($lastRunDatetime)
    {
        $now = (new \DateTime('now'))->setTime(0, 0, 0);
        $then = (new \DateTime($lastRunDatetime ?? 'now'))->setTime(0, 0, 0);
        
        if (0 !== $now->diff($then)->days) {
            return (new \App\Repositories\Statistics())->processDaily();
        }
    }

    public function counters()
    {
        $categories = db()->table('categories')->getTable();
        $listings = db()->table('listings')->getTable();
        $pivot = db()->table('category_listing')->getTable();

        db()->statement('
            UPDATE ' . $categories . ' AS categories
            LEFT JOIN (
                SELECT pivot.category_id, COUNT(*) as counter FROM ' . $pivot . ' AS pivot 
                LEFT JOIN ' . $listings . ' AS listings ON listings.id = pivot.listing_id 
                WHERE listings.status = "active" 
                    AND listings.active IS NOT NULL 
                GROUP BY pivot.category_id
            ) AS category_listing ON categories.id = category_listing.category_id
            SET categories.counter = (
                    SELECT COUNT(*) FROM ' . $listings . ' 
                    WHERE ' . $listings . '.category_id = categories.id
                        AND status = "active"
                        AND active IS NOT NULL
                )
                + IFNULL(category_listing.counter, 0)
            WHERE categories._parent_id IS NOT NULL
                AND categories._right = categories._left + 1'
        )
        ->execute();

        db()->statement('
            UPDATE ' . $categories . ' AS c1
            LEFT JOIN (
                SELECT id,
                (
                    SELECT SUM(counter)
                    FROM ' . $categories . ' AS c4
                    WHERE c4.type_id = c3.type_id
                        AND c4._right = c4._left + 1
                        AND c4._left > c3._left AND c4._right < c3._right
                ) AS total
                FROM ' . $categories . ' AS c3 
                WHERE c3._right <> c3._left + 1
                    AND c3._parent_id IS NOT NULL 
            ) AS c2 ON c1.id = c2.id
            SET c1.counter = c2.total
            WHERE c1._right <> c1._left + 1
                AND c1._parent_id IS NOT NULL'
        )
        ->execute();
    }

    public function orders()
    {
        //
        // 1. generate new invoice for expiring order
        //
        
        $qb = \App\Models\Order::query()
            ->where('status', 'active')
            ->where(db()->raw('NOW() >= end_datetime - INTERVAL ? DAY', [config()->billing->invoice_creation_days]))
            ->whereHasNot('invoice', function ($relation) {
                $relation->whereNull('end_datetime');
            })
            ->with('pricing')
            ->limit(50);

        foreach ($qb->get() as $order) {            
            if (null !== $order->subscription_id) {
                $invoice = $order->createInvoice(false);
                $order->invoice_id = $invoice->id;
                $invoice->setPaidWithoutEmailNotification();
            } else {
                $order->invoice_id = $order->createInvoice()->id;
            }

            $order->save();
        }

        //
        // 2. renew expired orders
        //

        $qb = \App\Models\Order::query()
            ->where('status', 'active')
            ->where(db()->raw('NOW() > end_datetime'))
            ->whereHas('invoice', function ($relation) {
                $relation
                    ->whereNull('end_datetime')
                    ->where('status', 'paid');
            })
            ->limit(50);

        foreach ($qb->with('invoice')->get() as $order) {
            $start = date('Y-m-d H:i:s');
            $end = $order->invoice->getBillingCycleEndDatetime();

            $order->start_datetime = $start;
            $order->end_datetime = $end;

            $order->invoice->start_datetime = $start;
            $order->invoice->end_datetime = $end;
            $order->invoice->save();

            $order->notification_1_sent = null;
            $order->notification_2_sent = null;
            $order->notification_3_sent = null;

            $order->save();
        }

        //
        // 3. suspend expired unpaid orders
        //

        $qb = \App\Models\Order::query()
            ->where('status', 'active')
            ->where(db()->raw('NOW() > end_datetime'))
            ->where(db()->raw('DATEDIFF(CURDATE(), DATE(end_datetime)) >= ?', [config()->billing->overdue_suspend_days]))
            ->whereHas('invoice', function ($relation) {
                $relation
                    ->whereNull('end_datetime')
                    ->where('status', '!=', 'paid');
            })
            ->limit(50);

        foreach ($qb->get() as $order) {
            $order->listing->status = 'suspended';
            $order->listing->save();
            
            $order->status = 'suspended';
            $order->subscription_id = null;
            $order->start_datetime = null;
            $order->end_datetime = null;

            $order->save();

            $emailData = [
                'id' => $order->user->id,
                'first_name' => $order->user->first_name,
                'last_name' => $order->user->last_name,
                'email' => $order->user->email,

                'listing_id' => $order->listing->id,
                'listing_title' => $order->listing->title,
                'listing_type_singular' => $order->listing->type->name_singular,
                'listing_type_plural' => $order->listing->type->name_plural,

                'invoice_id' => $order->invoice->id,
                'invoice_subtotal' => locale()->formatPrice($order->invoice->subtotal),
                'invoice_total' => locale()->formatPrice($order->invoice->total),
                'invoice_balance' => locale()->formatPrice($order->invoice->balance),
                'invoice_tax' => locale()->formatPrice($order->invoice->tax),
                'invoice_discount' => locale()->formatPrice($order->invoice->discount),
                'invoice_product' => $order->pricing->getNameWithProduct(),
                'invoice_addeddate' => locale()->formatDatetime($order->invoice->added_datetime, $order->user->timezone),
                'invoice_duedate' => locale()->formatDatetime($order->invoice->due_datetime, $order->user->timezone),

                'link' => route('account/checkout/' . $order->invoice->id),
            ];

            if (null !== $order->listing->type->get('active')) {
                (new \App\Repositories\EmailQueue())->push(
                    'user_order_suspended',
                    $order->user->id,
                    $emailData,
                    [$order->user->email => $order->user->getName()],
                    [config()->email->from_email => config()->email->from_name]
                );
            }

            (new \App\Repositories\EmailQueue())->push(
                'admin_order_suspended',
                null,
                $emailData,
                [config()->email->from_email => config()->email->from_name],
                [config()->email->from_email => config()->email->from_name]
            );
        }
    }

    public function invoices()
    {
        foreach ([1, 2, 3] as $count) {
            if ('0' != config()->billing->get('invoice_reminder_' . $count . '_days')) {
                $qb = \App\Models\Invoice::query()
                ->where('status', 'pending')
                ->where(db()->raw('DATEDIFF(CURDATE(), DATE(added_datetime)) = ?', [config()->billing->get('invoice_reminder_' . $count . '_days')]))
                ->whereHas('order', function ($relation) use ($count) {
                    $relation->whereNull('notification_' . $count . '_sent');
                });

                foreach ($qb->get() as $invoice) {
                    $invoice->order->put('notification_' . $count . '_sent', 1);
                    $invoice->order->save();

                    if (null !== $invoice->order->listing->type->get('active')) {
                        (new \App\Repositories\EmailQueue())->push(
                            'user_invoice_reminder_' . $count,
                            $invoice->order->user->id,
                            [
                                'id' => $invoice->order->user->id,
                                'first_name' => $invoice->order->user->first_name,
                                'last_name' => $invoice->order->user->last_name,
                                'email' => $invoice->order->user->email,

                                'listing_id' => $invoice->order->listing->id,
                                'listing_title' => $invoice->order->listing->title,
                                'listing_type_singular' => $invoice->order->listing->type->name_singular,
                                'listing_type_plural' => $invoice->order->listing->type->name_plural,

                                'invoice_id' => $invoice->id,
                                'invoice_subtotal' => locale()->formatPrice($invoice->subtotal),
                                'invoice_total' => locale()->formatPrice($invoice->total),
                                'invoice_balance' => locale()->formatPrice($invoice->balance),
                                'invoice_tax' => locale()->formatPrice($invoice->tax),
                                'invoice_discount' => locale()->formatPrice($invoice->discount),
                                'invoice_product' => $invoice->order->pricing->getNameWithProduct(),
                                'invoice_addeddate' => locale()->formatDatetime($invoice->added_datetime, $invoice->order->user->timezone),
                                'invoice_duedate' => locale()->formatDatetime($invoice->due_datetime, $invoice->order->user->timezone),

                                'link' => route('account/checkout/' . $invoice->id),
                            ],
                            [$invoice->order->user->email => $invoice->order->user->getName()],
                            [config()->email->from_email => config()->email->from_name]
                        );            
                    }
                }
            }
        }
    }

    public function export()
    {
        $export = \App\Models\Export::where('status', 'queued')
            ->orderBy('id')
            ->first();

        $listings = collect();

        $limit = 50;
        $offset = 0;

        if (null !== $export) {
            $export->status = 'running';
            $export->save();

            $currentLocale = locale()->getLocale();
            
            if (locale()->isSupported($export->language->locale)) {
                locale()->setLocale($export->language->locale);
            }
            
            $file = fopen($export->getPath(), 'w');

            $fields = $export->type
                ->fields()
                ->whereIn('id', $export->fields->pluck('id')->all())
                ->with('options')
                ->orderBy('weight')
                ->get();

            $accountFields = \App\Models\FieldGroup::find(1)
                ->fields()
                ->orderBy('weight')
                ->with([
                    'options',
                ])
                ->get()
                ->each(function ($field) {$field->name = 'account_' . $field->name; return $field;});

            $form = form()
                ->add('category_id', 'text')
                ->importWithoutConstraints($fields->merge($accountFields))
                ->remove('account_password');

            fputcsv($file, $form->getFields()->pluck('name')->all());

            while (null !== $listings) {
                $listings = $export
                    ->type
                    ->listings()
                    ->whereHas('order', function ($query) use ($export) {
                        $query->whereIn('pricing_id', $export->pricings->pluck('id')->all());
                    })
                    ->whereIn('category_id', $export->categories->pluck('id')->all())
                    ->offset($offset)
                    ->limit($limit)
                    ->with([
                        'user.data',
                        'data',
                    ])
                    ->get();

                foreach ($listings as $listing) {
                    $record = [];

                    $form
                        ->reset()
                        ->setValues([
                            'category_id' => (is_numeric($listing->get('category_id')) ? \App\Models\Category::find($listing->get('category_id'))->ancestorsAndSelfWithoutRoot()->get(['name'])->pluck('name')->implode('->') : ''),
                            'title' => $listing->get('title'),
                            'slug' => $listing->get('slug'),
                            'short_description' => $listing->get('short_description'),
                            'description' => $listing->get('description'),
                            'address' => $listing->get('address'),
                            'zip' => $listing->get('zip'),
                            'location_id' => $listing->get('location_id'),
                            'latitude' => $listing->get('latitude'),
                            'longitude' => $listing->get('longitude'),
                            'zoom' => $listing->get('zoom'),
                            'meta_title' => $listing->get('meta_title'),
                            'meta_keywords' => $listing->get('meta_keywords'),
                            'meta_description' => $listing->get('meta_description'),
                            'timezone' => $listing->get('timezone'),

                            'event_start_datetime' => $listing->get('event_start_datetime'),
                            'event_frequency' => $listing->get('event_frequency'),
                            'event_interval' => $listing->get('event_interval'),
                            'event_weekdays' => $listing->get('event_weekdays'),
                            'event_weeks' => $listing->get('event_weeks'),
                            'event_dates' => $listing->get('event_dates'),
                            'event_end_datetime' => $listing->get('event_end_datetime'),
                            'event_rsvp' => $listing->get('event_rsvp'),

                            'offer_start_datetime' => $listing->get('offer_start_datetime'),
                            'offer_end_datetime' => $listing->get('offer_end_datetime'),
                            'offer_price' => $listing->get('offer_price'),
                            'offer_discount_type' => $listing->get('offer_discount_type'),
                            'offer_discount' => $listing->get('offer_discount'),
                            'offer_count' => $listing->get('offer_count'),
                            'offer_terms' => $listing->get('offer_terms'),
                            'offer_redeem' => $listing->get('offer_redeem'),

                            'account_first_name' => $listing->user->get('first_name'),
                            'account_last_name' => $listing->user->get('last_name'),
                            'account_address' => $listing->user->get('address'),
                            'account_zip' => $listing->user->get('zip'),
                            'account_location_id' => $listing->user->get('location_id'),
                            'account_latitude' => $listing->user->get('latitude'),
                            'account_longitude' => $listing->user->get('longitude'),
                            'account_zoom' => $listing->user->get('zoom'),
                            'account_email' => $listing->user->get('email'),
                            'account_timezone' => $listing->user->get('timezone'),
                        ])
                        ->setValues($listing->data->pluck('value', 'field_name')->all());

                    $userData = [];

                    foreach ($listing->user->data->all() as $data) {
                        $userData['account_' . $data->field_name] = $data->value;
                    }

                    $form->setValues($userData);

                    foreach ($form->getFields() as $field) {
                        $asis = [
                            'event_frequency',
                            'event_weekdays',
                            'event_weeks',
                            'offer_discount_type',
                        ];

                        $record[] = (false === in_array($field->getName(), $asis)) ? $field->exportValue() : $field->getValue();
                    }

                    fputcsv($file, $record);
                }

                $offset += $limit;

                if ($listings->count() < $limit) {
                    $listings = null;
                }

                set_time_limit(30);
            }

            locale()->setLocale($currentLocale);

            fclose($file);

            $export->status = 'done';
            $export->save();
        }
    }

    public function import()
    {
        $failed = false;

        if (null !== \App\Models\Import::where('status', 'running')->first()) {
            return;
        }

        $import = \App\Models\Import::where('status', 'queued')
            ->orderBy('id')
            ->first();

        if (null !== $import) {
            $import->status = 'running';
            $import->save();

            $file = fopen($import->getPath(), 'r');
            $log = fopen($import->getLogPath(), 'w');

            if (null === \App\Models\Type::where('id', $import->type_id)->whereNull('deleted')->first()) {
                fputs($log, __('admin.import.error.type', ['id' => $import->type_id]) . "\n");

                $failed = true;
            }

            if (null === $import->pricing) {
                fputs($log, __('admin.import.error.pricing', ['id' => $import->pricing_id]) . "\n");

                $failed = true;
            }
            
            if (null === $import->user) {
                fputs($log, __('admin.import.error.user', ['id' => $import->user_id]) . "\n");

                $failed = true;
            }

            if (null === $import->language) {
                fputs($log, __('admin.import.error.language', ['id' => $import->language_id]) . "\n");

                $failed = true;
            }

            $header = fgetcsv($file);

            if (null === $header || false === $header || false === is_array($header) || null === $header[0]) {
                fputs($log, __('admin.import.error.parse') . "\n");

                $failed = true;
            }

            if (count($header) !== count(array_unique($header))) {
                fputs($log, __('admin.import.error.duplicate') . "\n");

                $failed = true;
            }

            if (substr($header[0], 0, 3) === pack('CCC', 0xEF, 0xBB, 0xBF)) {
                $header[0] = substr($header[0], 3);
            }

            $line = 1;

            $fields = $import->type->fields()
                ->where('listingfieldgroup_id', 1)
                ->whereNull('customizable')
                ->get()
                ->pluck('name')
                ->push('category_id')
                ->all();

            foreach ($fields as $key => $field) {
                if (false !== in_array($field, $header)) {
                    unset($fields[$key]);
                }
            }

            if (count($fields) > 0) {
                fputs($log, __('admin.import.error.fields', ['fields' => implode(', ', $fields)]) . "\n");

                $failed = true;
            }

            if (false === $failed) {
                $fields = $import->type
                    ->fields()
                    ->where('listingfieldgroup_id', 1)
                    ->with([
                        'options',
                        'constraints',
                    ])
                    ->get();
                
                $form = form()->import($fields);

                $accountFields = \App\Models\FieldGroup::find(1)
                    ->fields()
                    ->with([
                        'options',
                        'constraints',
                    ])
                    ->get();

                $accountForm = form()->import($accountFields)
                    ->remove('password');
                
                while (false !== $csv = fgetcsv($file)) {                    
                    $line++;

                    if (false === is_array($csv) || null === $csv[0]) {
                        continue;
                    }

                    $input = collect();
                    $extraCategories = [];

                    $listing = new \App\Models\Listing();
                    $listing->user_id = $import->user_id;
                    $listing->import_id = $import->id;
                    $listing->active = $import->active;
                    $listing->claimed = $import->claimed; 
                    $listing->type_id = $import->type_id;
                    $listing->rating = 0;
                    $listing->review_count = 0;
                    $listing->added_datetime = date("Y-m-d H:i:s");

                    $order = new \App\Models\Order();
                    $order->user_id = $import->user_id;
                    $order->pricing_id = $import->pricing_id;

                    foreach ($header as $key => $name) {
                        if (false === array_key_exists($key, $csv)) {
                            fputs($log, __('admin.import.error.value', ['line' => $line, 'field' => $name]) . "\n");

                            continue 2;
                        }

                        if (false === mb_detect_encoding($csv[$key], 'UTF-8', true)) {
                            fputs($log, __('admin.import.error.encoding', ['line' => $line, 'field' => $name]) . "\n");

                            continue 2;
                        }

                        if (false !== in_array($name, ['category_id', 'category_id_1', 'category_id_2', 'category_id_3', 'category_id_4', 'category_id_5'])) {
                            $root = (new \App\Models\Category)->getRoot($import->type_id);

                            $current = $root;                    

                            $categories = explode('->', $csv[$key]);

                            foreach ($categories as $category) {
                                $category = trim(e($category));
                                
                                if ('' == $category && 'category_id' == $name) {
                                    fputs($log, __('admin.import.error.category', ['line' => $line]) . "\n");

                                    continue 3;
                                }

                                if ('' != $category) {
                                    $temp = \App\Models\Category::where('type_id', $import->type_id)
                                        ->where('_parent_id', $current->id)
                                        ->where('name', 'like', '%"' . $import->language->locale . '":"' . $category . '"%')
                                        ->first();

                                    if (null === $temp) {
                                        $temp = new \App\Models\Category();
                                        $temp->type_id = $import->type_id;
                                        $temp->appendTo($current);

                                        $temp->fill([
                                            'active' => 1,
                                            'featured' => null,
                                            'slug' => slugify(d($category)),
                                            'icon' => 'far fa-circle',
                                            'marker_color' => 'red',
                                            'icon_color' => 'white',
                                            'logo_id' => bin2hex(random_bytes(16)),
                                            'header_id' => bin2hex(random_bytes(16)),
                                        ]);

                                        $temp->setTranslation('name', $category, config()->app->locale_fallback);

                                        if (config()->app->locale_fallback != $import->language->locale) {
                                            $temp->setTranslation('name', $category, $import->language->locale);
                                        }

                                        $temp->save();

                                        $temp->fields()->attach($fields->pluck('id')->all());
                                        $temp->products()->attach($import->pricing->product_id);
                                    }

                                    $current = $temp;

                                    if ('category_id' == $name) {
                                        $listing->category_id = $current->id;
                                    } else {
                                        $extraCategories[] = $current->id;
                                    }
                                }
                            }
                        } else if ('title' == $name) {
                            $listing->title = mb_substr(trim($csv[$key]), 0, 250);
                        } else if ('slug' == $name) {
                            $slug = substr(slugify(trim($csv[$key])), 0, 250);
                            
                            if ('' == $slug || \App\Models\Listing::where('slug', $slug)->where('type_id', $import->type_id)->count() > 0) {
                                fputs($log, __('admin.import.error.slug', ['line' => $line]) . "\n");

                                continue 2;
                            }

                            $listing->slug = $slug;
                        } else if ('added_datetime' == $name) {
                            if ('' != $csv[$key] && false !== \DateTime::createFromFormat('Y-m-d H:i:s', $csv[$key])) {
                                $listing->added_datetime = trim($csv[$key]);
                            }
                        } else if ('account_email' == $name) {
                            if (null !== $user = \App\Models\User::where('email', $csv[$key])->first()) {
                                $listing->user_id = $user->id;
                                $order->user_id = $user->id;
                            } else if ('' != $csv[$key] && false !== filter_var($csv[$key], FILTER_VALIDATE_EMAIL)) {
                                $userInput = collect();

                                $user = new \App\Models\User();
                                $user->active = 1;
                                $user->email_verified = 1;
                                $user->taxable = 1;
                                $user->email = $csv[$key];
                                $user->first_name = 'Listing';
                                $user->last_name = 'Owner';
                                $user->latitude = '0';
                                $user->longitude = '0';
                                $user->zoom = '17';
                                $user->timezone = '+0000';
                                $user->added_datetime = date('Y-m-d H:i:s');

                                foreach ($header as $key => $name) {
                                    if (0 === strpos($name, 'account_')) {
                                        if (false !== array_key_exists($key, $csv)) {
                                            $name = substr($name, 8);

                                            if ('latitude' == $name || 'longitude' == $name) {
                                                if ('' == trim($csv[$key])) {
                                                    fputs($log, __('admin.import.error.required', ['line' => $line, 'field' => 'account_' . $name]) . "\n");

                                                    continue 3;
                                                }

                                                if (false === is_numeric($csv[$key]) || 180 < trim($csv[$key]) || -180 > trim($csv[$key])) {
                                                    fputs($log, __('admin.import.error.invalid', ['line' => $line, 'field' => 'account_' . $name]) . "\n");

                                                    continue 3;
                                                }

                                                $csv[$key] = number_format($csv[$key], 6, '.', '');
                                            }

                                            if ('zoom' == $name) {
                                                if (false === is_numeric($csv[$key]) || $csv[$key] < 0 || $csv[$key] > 20) {
                                                    fputs($log, __('admin.import.error.invalid', ['line' => $line, 'field' => 'account_' . $name]) . "\n");

                                                    continue 3;
                                                }

                                                $csv[$key] = number_format($csv[$key], 0, '.', '');
                                            }

                                            if (null !== $accountField = $accountForm->get($name)) {
                                                if (false !== $value = $accountField->importValue($csv[$key], $accountFields->where('name', $name)->first(), $import->language->locale)) {
                                                    if (null === $accountFields->where('name', $name)->first()->customizable) {
                                                        $user->put($name, $value);
                                                    } else {
                                                        $userInput->put($name, $value);
                                                    }
                                                } else {
                                                    fputs($log, __('admin.import.error.account_value', ['line' => $line, 'field' => $name]) . "\n");

                                                    continue 3;
                                                }
                                            }
                                        }
                                    }
                                }

                                $user->saveWithData($userInput);

                                $account = new \App\Models\Account();
                                $account->setPassword(bin2hex(random_bytes(12)));
                                $account->provider = 'native';
                                $account->usergroup_id = config()->account->default_group;
                                $user->account()->save($account);

                                $listing->user_id = $user->id;
                                $order->user_id = $user->id;
                            }
                        } else {
                            if (null !== $field = $form->get($name)) {
                                if (null === $fields->where('name', $name)->first()->customizable) {
                                    if (false !== $field->isRequired() && '' == $csv[$key]) {
                                        fputs($log, __('admin.import.error.required', ['line' => $line, 'field' => $name]) . "\n");

                                        continue 2;
                                    }
                                }

                                $value = false;

                                if ('event_frequency' == $name) {
                                    if (false !== in_array($csv[$key], ['once', 'daily', 'weekly', 'monthly', 'yearly', 'custom'])) {
                                        $value = $csv[$key];
                                    }
                                } else if ('event_weekdays' == $name) {
                                    if ('' == trim($csv[$key])) {
                                        $value = '';
                                    } else {
                                        $temp = array_map('trim', explode(',', $csv[$key]));
                                        if (count(array_diff($temp, [7, 1, 2, 3, 4, 5, 6])) == 0) {
                                            $value = implode(',', $temp);
                                        }
                                    }
                                } else if ('event_weeks' == $name) {
                                    if ('' == trim($csv[$key])) {
                                        $value = '';
                                    } else {
                                        $temp = array_map('trim', explode(',', $csv[$key]));
                                        if (count(array_diff($temp, [1, 2, 3, 4, 5])) == 0) {
                                            $value = implode(',', $temp);
                                        }
                                    }
                                } else if ('offer_discount_type' == $name) {
                                    if (false !== in_array($csv[$key], ['fixed', 'percentage'])) {
                                        $value = $csv[$key];
                                    }
                                } else if ('latitude' == $name || 'longitude' == $name) {
                                    if (false !== is_numeric($csv[$key]) && 180 > trim($csv[$key]) && -180 < trim($csv[$key])) {
                                        $value = number_format($csv[$key], 6, '.', '');
                                    }
                                } else if ('zoom' == $name) {
                                    if (false !== is_numeric($csv[$key]) && $csv[$key] >= 0 && $csv[$key] <= 20) {
                                        $value = number_format($csv[$key], 0, '.', '');
                                    }
                                } else {
                                    $value = $field->importValue($csv[$key], $fields->where('name', $name)->first(), $import->language->locale);
                                }

                                if (false === $value) {
                                    fputs($log, __('admin.import.error.invalid', ['line' => $line, 'field' => $name]) . "\n");

                                    continue 2;
                                }

                                if (null === $fields->where('name', $name)->first()->customizable) {
                                    $listing->put($name, $value);
                                } else {
                                    $input->put($name, $value);
                                }
                            }
                        }
                    }

                    if ('Event' == $import->type->type) {
                        $dates = $listing->getEventDates();

                        if (false === $dates || count($dates) == 0) {
                            fputs($log, __('admin.import.error.dates', ['line' => $line]) . "\n");

                            continue;
                        }
                    }

                    $listing->saveWithData($input);

                    $listing->order()->save($order);

                    $order->activate($import->pricing_id, true, (null !== $import->notification ? true : false));

                    if ('Event' == $import->type->type) {
                        $listing->saveEventDates($dates);
                    }

                    $listing->categories()->sync(array_unique($extraCategories));

                    set_time_limit(30);
                }
            }

            fclose($file);
            fclose($log);

            $import->status = 'done';
            $import->save();        
        }
    }

    public function sitemap()
    {
        foreach (['pages', 'types', 'listings', 'categories', 'locations'] as $sitemap) {
            if (is_dir(PATH . DS . 'sitemap' . DS . $sitemap)) {
                foreach (glob(PATH . DS . 'sitemap' . DS . $sitemap . DS . '*') as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
            }
        }
        
        $robotsFile = fopen(PATH . DS . 'robots.txt', 'w');

        $data = str_replace('{sitemap}', route('sitemap.xml'), config()->other->robots_txt);

        fputs($robotsFile, $data);
        fclose($robotsFile);

        $types = \App\Models\Type::whereNull('deleted')->whereNotNull('active')->get();

        $typesArray = $types->pluck('slug', 'id')->all();

        $index = new \App\Repositories\SitemapIndex();

        foreach (['pages', 'types', 'listings', 'categories', 'locations'] as $type) {
            $sitemap = new \App\Repositories\Sitemap($type, $index);

            switch ($type) {
                case 'pages':
                    $pages = \App\Models\Page::whereNull('type_id')
                            ->whereNotNull('active')
                            ->get(['slug']);

                    $sitemap->push(route(''), null, config()->sitemap->changefreq->index, config()->sitemap->priority->index);

                    foreach ($pages as $page) {
                        if (false === in_array($page->slug, ['error/404', 'error/405', 'account/profile', 'account/bookmarks', 'account/messages', 'account/messages/view', 'account/reviews', 'account/reviews/view', 'account/claims', 'account/invoices', 'account/invoices/view', 'account/manage/type', 'account/manage/type/create', 'account/manage/type/update', 'account/manage/type/summary', 'account/manage/type/reviews', 'account/checkout', 'account/checkout/gateway'])) {
                            $sitemap->push(route($page->slug), null, config()->sitemap->changefreq->pages, config()->sitemap->priority->pages);
                        }
                    }

                    break;
                case 'types':
                    foreach ($typesArray as $slug) {
                        $sitemap->push(route($slug), null, config()->sitemap->changefreq->types->index, config()->sitemap->priority->types->index);
                        $sitemap->push(route($slug . '/search'), null, config()->sitemap->changefreq->types->search, config()->sitemap->priority->types->search);
                        $sitemap->push(route($slug . '/pricing'), null, config()->sitemap->changefreq->types->pricing, config()->sitemap->priority->types->pricing);
                    }

                    break;
                case 'listings':
                    $listings = collect();

                    $limit = 1000;
                    $offset = 0;

                    if (count($typesArray) == 0) {
                        break;
                    }

                    $reviewableTypesArray = $types->where('reviewable', 1)->pluck('slug', 'id')->all();

                    while (null !== $listings) {
                        $listings = \App\Models\Listing::whereNotNull('_page')
                            ->whereNotNull('active')
                            ->where('status', 'active')
                            ->whereIn('type_id', array_keys($typesArray))
                            ->offset($offset)
                            ->limit($limit)
                            ->get(['type_id', 'claimed', 'slug', '_send_message', '_reviews', 'added_datetime', 'updated_datetime']);

                        foreach ($listings as $listing) {
                            $lastmod = null !== $listing->get('updated_datetime') ? locale()->formatDatetimeISO8601($listing->get('updated_datetime')) : locale()->formatDatetimeISO8601($listing->get('added_datetime'));
                            
                            $sitemap->push(route($typesArray[$listing->type_id] . '/' . $listing->slug), $lastmod, config()->sitemap->changefreq->listings->index, config()->sitemap->priority->listings->index);

                            if (null !== $listing->get('_send_message')) {
//                                $sitemap->push(route($typesArray[$listing->type_id] . '/' . $listing->slug . '/send-message'), $lastmod, config()->sitemap->changefreq->listings->send_message, config()->sitemap->priority->listings->send_message);
                            }

                            if (null !== $listing->get('_reviews') && array_key_exists($listing->type_id, $reviewableTypesArray)) {
                                $sitemap->push(route($typesArray[$listing->type_id] . '/' . $listing->slug . '/reviews'), $lastmod, config()->sitemap->changefreq->listings->reviews, config()->sitemap->priority->listings->reviews);
//                                $sitemap->push(route($typesArray[$listing->type_id] . '/' . $listing->slug . '/add-review'), $lastmod, config()->sitemap->changefreq->listings->add_review, config()->sitemap->priority->listings->add_review);
                            }

                            if (null === $listing->get('claimed')) {
//                                $sitemap->push(route($typesArray[$listing->type_id] . '/' . $listing->slug . '/claim'), $lastmod, config()->sitemap->changefreq->listings->claim, config()->sitemap->priority->listings->claim);
                            }
                        }

                        $offset += $limit;

                        if ($listings->count() < $limit) {
                            $listings = null;
                        }

                        set_time_limit(10);
                    }

                    break;
                case 'categories':
                    $categories = collect();

                    $limit = 1000;
                    $offset = 0;

                    if (count($typesArray) == 0) {
                        break;
                    }

                    while (null !== $categories) {
                        $categories = \App\Models\Category::whereNotNull('active')->whereNotNull('_parent_id')
                            ->whereIn('type_id', array_keys($typesArray))
                            ->offset($offset)
                            ->limit($limit)
                            ->get(['type_id', 'slug']);

                        foreach ($categories as $category) {
                            $sitemap->push(route($typesArray[$category->type_id] . '/' . $category->slug), null, config()->sitemap->changefreq->categories, config()->sitemap->priority->categories);
                        }

                        $offset += $limit;

                        if ($categories->count() < $limit) {
                            $categories = null;
                        }

                        set_time_limit(10);
                    }

                    break;
                case 'locations':
                    $locations = collect();

                    $limit = 1000;
                    $offset = 0;

                    $localizableTypesArray = $types->where('localizable', 1)->pluck('slug', 'id')->all();

                    if (count($localizableTypesArray) > 0) {
                        while (null !== $locations) {

                            $locations = \App\Models\Location::whereNotNull('active')->whereNotNull('_parent_id')
                                ->offset($offset)
                                ->limit($limit)
                                ->get(['slug']);
                        
                            foreach ($locations as $location) {
                                foreach ($localizableTypesArray as $id => $slug) {
                                    $sitemap->push(route($slug . '/' . $location->slug), null, config()->sitemap->changefreq->locations, config()->sitemap->priority->locations);
                                }
                            }

                            $offset += $limit;

                            if ($locations->count() < $limit) {
                                $locations = null;
                            }

                            set_time_limit(10);
                        }
                    }

                    break;
            }

            $sitemap->render();
        }

        $index->render();
    }

}

<?php

return [
    '/cron' => ['GET', 'Cron/Index'],
    '/css/{slug}/style.css' => ['GET', 'Css/Index'],
    '/media/{id}/{name}' => ['GET', 'Media/Index'],
    '/media/{id}/{type}/{name}' => ['GET', 'Media/Index'],
    '/ajax/widget/{parameter}' => [['GET', 'POST'], 'Ajax/Widget'],
    '/ajax/uploadasset' => [['GET', 'POST'], 'Ajax/UploadAsset'],
    '/ajax/upload/{parameter}' => [['GET', 'POST'], 'Ajax/Upload'],
    '/ajax/mailtest' => ['POST', 'Ajax/Mailtest'],
    '/ajax/cascading' => ['POST', 'Ajax/Cascading'],
    '/ajax/sort' => ['POST', 'Ajax/Sort'],
    '/ajax/switch' => ['POST', 'Ajax/Switch'],
    '/ajax/slugify' => ['POST', 'Ajax/Slugify'],
    '/ajax/user' => ['GET', 'Ajax/User'],
    '/ajax/listing' => ['GET', 'Ajax/Listing'],
    '/ajax/category' => ['GET', 'Ajax/Category'],
    '/ajax/location' => ['GET', 'Ajax/Location'],
    '/ajax/hours' => ['POST', 'Ajax/Hours'],
    '/ajax/bookmark' => ['POST', 'Ajax/Bookmark'],
    '/ajax/click-to-call' => ['POST', 'Ajax/ClickToCall'],
    '/ajax/visit-website' => ['POST', 'Ajax/VisitWebsite'],
    '/ajax/map' => ['POST', 'Ajax/Map'],
    '/ajax/hash' => ['GET', 'Ajax/Hash'],
    '/ajax/ai' => ['POST', 'Ajax/Ai'],

    '/account' => [['GET', 'POST'], 'Account/Index/Index'],
    '/account/create' => [['GET', 'POST'], 'Account/Account/Create'],
    '/account/login[/{provider}]' => [['GET', 'POST'], 'Account/Account/Login'],
    '/account/logout' => ['GET', 'Account/Account/Logout'],
    '/account/callback/{provider}' => ['GET', 'Account/Account/Callback'],
    '/account/verification[/{code}]' => [['GET', 'POST'], 'Account/Account/Verification'],
    '/account/password-reminder' => [['GET', 'POST'], 'Account/Account/PasswordReminder'],
    '/account/password-reset[/{code}]' => [['GET', 'POST'], 'Account/Account/PasswordReset'],
    '/account/profile' => [['GET', 'POST'], 'Account/Index/Profile'],

    '/account/claims' => [['GET', 'POST'], 'Account/Claims/Index'],

    '/account/reviews' => [['GET', 'POST'], 'Account/Reviews/Index'],
    '/account/reviews/{id}' => [['GET', 'POST'], 'Account/Reviews/View'],

    '/account/bookmarks' => [['GET', 'POST'], 'Account/Bookmarks/Index'],
    '/account/bookmarks/delete/{slug}' => [['GET', 'POST'], 'Account/Bookmarks/Delete'],

    '/account/manage/{type}' => [['GET', 'POST'], 'Account/Listings/Index'],
    '/account/manage/{type}/summary/{slug}' => [['GET', 'POST'], 'Account/Listings/Summary'],
    '/account/manage/{type}/create' => [['GET', 'POST'], 'Account/Listings/Create'],
    '/account/manage/{type}/update/{slug}' => [['GET', 'POST'], 'Account/Listings/Update'],
    '/account/manage/{type}/reviews/{slug}' => [['GET', 'POST'], 'Account/Listings/Reviews'],

    '/account/invoices' => [['GET', 'POST'], 'Account/Invoices/Index'],
    '/account/invoices/print/{id}' => [['GET', 'POST'], 'Account/Invoices/Print'],
    '/account/invoices/{id}' => [['GET', 'POST'], 'Account/Invoices/View'],

    '/account/messages' => [['GET', 'POST'], 'Account/Messages/Index'],
    '/account/messages/{id}' => [['GET', 'POST'], 'Account/Messages/View'],

    '/account/checkout/success/{transaction}' => [['GET','POST'], 'Account/Checkout/Success'],
    '/account/checkout/failed/{transaction}' => ['GET', 'Account/Checkout/Cancel'],
    '/account/checkout/notify/{gateway}' => [['GET', 'POST'], 'Account/Notify/Notify'],
    '/account/checkout/{invoice}' => [['GET', 'POST'], 'Account/Checkout/Index'],
    '/account/checkout/{gateway}/{invoice}' => [['GET', 'POST'], 'Account/Checkout/Form'],

    '/{admin}' => [['GET', 'POST'], 'Admin/Index/Index'],
    '/{admin}/login' => [['GET', 'POST'], 'Admin/Account/Login'],
    '/{admin}/logout' => ['GET', 'Admin/Account/Logout'],

    '/{admin}/settings[/{group}]' => [['GET', 'POST'], 'Admin/Settings/Index'],

    '/{admin}/types' => [['GET', 'POST'], 'Admin/Types/Index'],
    '/{admin}/types/create' => [['GET', 'POST'], 'Admin/Types/Create'],
    '/{admin}/types/update/{id}' => [['GET', 'POST'], 'Admin/Types/Update'],
    '/{admin}/types/delete/{id}' => ['GET', 'Admin/Types/Delete'],

    '/{admin}/themes' => [['GET', 'POST'], 'Admin/Themes/Index'],
    '/{admin}/themes/create' => [['GET', 'POST'], 'Admin/Themes/Create'],
    '/{admin}/themes/update/{slug}' => [['GET', 'POST'], 'Admin/Themes/Update'],
    '/{admin}/themes/delete/{slug}' => ['GET', 'Admin/Themes/Delete'],

    '/{admin}/payment-gateways' => [['GET', 'POST'], 'Admin/Gateways/Index'],
    '/{admin}/payment-gateways/update/{id}' => [['GET', 'POST'], 'Admin/Gateways/Update'],

    '/{admin}/widget-field-groups' => ['GET', 'Admin/WidgetFieldGroups/Index'],
    '/{admin}/widget-field-groups/create' => [['GET', 'POST'], 'Admin/WidgetFieldGroups/Create'],
    '/{admin}/widget-field-groups/update/{id}' => [['GET', 'POST'], 'Admin/WidgetFieldGroups/Update'],
    '/{admin}/widget-field-groups/delete/{id}' => ['GET', 'Admin/WidgetFieldGroups/Delete'],

    '/{admin}/widget-fields/{group}' => ['GET', 'Admin/WidgetFields/Index'],
    '/{admin}/widget-fields/{group}/create' => [['GET', 'POST'], 'Admin/WidgetFields/Create'],
    '/{admin}/widget-fields/{group}/update/{id}' => [['GET', 'POST'], 'Admin/WidgetFields/Update'],
    '/{admin}/widget-fields/{group}/delete/{id}' => ['GET', 'Admin/WidgetFields/Delete'],

    '/{admin}/widget-field-constraints/{group}' => [['GET', 'POST'], 'Admin/WidgetFieldConstraints/Index'],
    '/{admin}/widget-field-constraints/{group}/create' => [['GET', 'POST'], 'Admin/WidgetFieldConstraints/Create'],
    '/{admin}/widget-field-constraints/{group}/update/{id}' => [['GET', 'POST'], 'Admin/WidgetFieldConstraints/Update'],
    '/{admin}/widget-field-constraints/{group}/delete/{id}' => ['GET', 'Admin/WidgetFieldConstraints/Delete'],

    '/{admin}/widget-field-options/{group}' => [['GET', 'POST'], 'Admin/WidgetFieldOptions/Index'],
    '/{admin}/widget-field-options/{group}/create' => [['GET', 'POST'], 'Admin/WidgetFieldOptions/Create'],
    '/{admin}/widget-field-options/{group}/update/{id}' => [['GET', 'POST'], 'Admin/WidgetFieldOptions/Update'],
    '/{admin}/widget-field-options/{group}/delete/{id}' => ['GET', 'Admin/WidgetFieldOptions/Delete'],

    '/{admin}/{type}-import' => [['GET', 'POST'], 'Admin/Import/Index'],
    '/{admin}/{type}-import/create' => [['GET', 'POST'], 'Admin/Import/Create'],
    '/{admin}/{type}-import/download/{id}' => [['GET', 'POST'], 'Admin/Import/Download'],
    '/{admin}/{type}-import/delete/{id}' => [['GET', 'POST'], 'Admin/Import/Delete'],

    '/{admin}/{type}-export' => [['GET', 'POST'], 'Admin/Export/Index'],
    '/{admin}/{type}-export/create' => [['GET', 'POST'], 'Admin/Export/Create'],
    '/{admin}/{type}-export/download/{id}' => [['GET', 'POST'], 'Admin/Export/Download'],
    '/{admin}/{type}-export/delete/{id}' => [['GET', 'POST'], 'Admin/Export/Delete'],

    '/{admin}/{type}-broken-links' => [['GET', 'POST'], 'Admin/Deadlinks/Index'],
    '/{admin}/{type}-broken-links/update/{id}' => [['GET', 'POST'], 'Admin/Deadlinks/Update'],
    '/{admin}/{type}-invalid-backlinks' => [['GET', 'POST'], 'Admin/Backlinks/Index'],
    '/{admin}/{type}-invalid-backlinks/update/{id}' => [['GET', 'POST'], 'Admin/Backlinks/Update'],

    '/{admin}/{group}-fields/{type}' => [['GET', 'POST'], 'Admin/ListingFields/Index'],
    '/{admin}/{group}-fields/{type}/create' => [['GET', 'POST'], 'Admin/ListingFields/Create'],
    '/{admin}/{group}-fields/{type}/update/{id}' => [['GET', 'POST'], 'Admin/ListingFields/Update'],
    '/{admin}/{group}-fields/{type}/delete/{id}' => ['GET', 'Admin/ListingFields/Delete'],

    '/{admin}/{group}-field-constraints/{type}' => [['GET', 'POST'], 'Admin/ListingFieldConstraints/Index'],
    '/{admin}/{group}-field-constraints/{type}/create' => [['GET', 'POST'], 'Admin/ListingFieldConstraints/Create'],
    '/{admin}/{group}-field-constraints/{type}/update/{id}' => [['GET', 'POST'], 'Admin/ListingFieldConstraints/Update'],
    '/{admin}/{group}-field-constraints/{type}/delete/{id}' => ['GET', 'Admin/ListingFieldConstraints/Delete'],

    '/{admin}/{group}-field-options/{type}' => [['GET', 'POST'], 'Admin/ListingFieldOptions/Index'],
    '/{admin}/{group}-field-options/{type}/create' => [['GET', 'POST'], 'Admin/ListingFieldOptions/Create'],
    '/{admin}/{group}-field-options/{type}/create-multiple' => [['GET', 'POST'], 'Admin/ListingFieldOptions/CreateMultiple'],
    '/{admin}/{group}-field-options/{type}/update/{id}' => [['GET', 'POST'], 'Admin/ListingFieldOptions/Update'],
    '/{admin}/{group}-field-options/{type}/delete/{id}' => ['GET', 'Admin/ListingFieldOptions/Delete'],

    '/{admin}/products/{type}' => [['GET', 'POST'], 'Admin/Products/Index'],
    '/{admin}/products/{type}/create' => [['GET', 'POST'], 'Admin/Products/Create'],
    '/{admin}/products/{type}/update/{id}' => [['GET', 'POST'], 'Admin/Products/Update'],
    '/{admin}/products/{type}/delete/{id}' => ['GET', 'Admin/Products/Delete'],

    '/{admin}/pricings/{type}' => [['GET', 'POST'], 'Admin/Pricings/Index'],
    '/{admin}/pricings/{type}/create' => [['GET', 'POST'], 'Admin/Pricings/Create'],
    '/{admin}/pricings/{type}/update/{id}' => [['GET', 'POST'], 'Admin/Pricings/Update'],
    '/{admin}/pricings/{type}/delete/{id}' => [['GET', 'POST'], 'Admin/Pricings/Delete'],

    '/{admin}/discounts' => [['GET', 'POST'], 'Admin/Discounts/Index'],
    '/{admin}/discounts/create' => [['GET', 'POST'], 'Admin/Discounts/Create'],
    '/{admin}/discounts/update/{id}' => [['GET', 'POST'], 'Admin/Discounts/Update'],
    '/{admin}/discounts/delete/{id}' => [['GET', 'POST'], 'Admin/Discounts/Delete'],

    '/{admin}/manage/{type}' => [['GET', 'POST'], 'Admin/Listings/Index'],
    '/{admin}/manage/{type}/approve' => [['GET', 'POST'], 'Admin/Listings/Approve'],
    '/{admin}/manage/{type}/approve-updates' => [['GET', 'POST'], 'Admin/Listings/ApproveUpdates'],
    '/{admin}/manage/{type}/approve-update/{id}' => [['GET', 'POST'], 'Admin/Listings/ApproveUpdate'],
    '/{admin}/manage/{type}/reject-update/{id}' => [['GET', 'POST'], 'Admin/Listings/RejectUpdate'],
    '/{admin}/manage/{type}/summary/{id}' => [['GET', 'POST'], 'Admin/Listings/Summary'],
    '/{admin}/manage/{type}/create' => [['GET', 'POST'], 'Admin/Listings/Create'],
    '/{admin}/manage/{type}/update/{id}' => [['GET', 'POST'], 'Admin/Listings/Update'],
    '/{admin}/manage/{type}/delete/{id}' => [['GET', 'POST'], 'Admin/Listings/Delete'],

    '/{admin}/files' => [['GET', 'POST'], 'Admin/Files/Index'],
    '/{admin}/files/delete/{id}' => [['GET', 'POST'], 'Admin/Files/Delete'],

    '/{admin}/users' => [['GET', 'POST'], 'Admin/Users/Index'],
    '/{admin}/users/approve' => [['GET', 'POST'], 'Admin/Users/Approve'],
    '/{admin}/users/summary/{id}' => [['GET', 'POST'], 'Admin/Users/Summary'],
    '/{admin}/users/create' => [['GET', 'POST'], 'Admin/Users/Create'],
    '/{admin}/users/update/{id}' => [['GET', 'POST'], 'Admin/Users/Update'],
    '/{admin}/users/delete/{id}' => ['GET', 'Admin/Users/Delete'],

    '/{admin}/user-groups' => [['GET', 'POST'], 'Admin/UserGroups/Index'],
    '/{admin}/user-groups/create' => [['GET', 'POST'], 'Admin/UserGroups/Create'],
    '/{admin}/user-groups/update/{id}' => [['GET', 'POST'], 'Admin/UserGroups/Update'],
    '/{admin}/user-groups/delete/{id}' => ['GET', 'Admin/UserGroups/Delete'],

    '/{admin}/fields/{group}' => ['GET', 'Admin/Fields/Index'],
    '/{admin}/fields/{group}/create' => [['GET', 'POST'], 'Admin/Fields/Create'],
    '/{admin}/fields/{group}/update/{id}' => [['GET', 'POST'], 'Admin/Fields/Update'],
    '/{admin}/fields/{group}/delete/{id}' => ['GET', 'Admin/Fields/Delete'],

    '/{admin}/field-constraints/{group}' => [['GET', 'POST'], 'Admin/FieldConstraints/Index'],
    '/{admin}/field-constraints/{group}/create' => [['GET', 'POST'], 'Admin/FieldConstraints/Create'],
    '/{admin}/field-constraints/{group}/update/{id}' => [['GET', 'POST'], 'Admin/FieldConstraints/Update'],
    '/{admin}/field-constraints/{group}/delete/{id}' => ['GET', 'Admin/FieldConstraints/Delete'],

    '/{admin}/field-options/{group}' => [['GET', 'POST'], 'Admin/FieldOptions/Index'],
    '/{admin}/field-options/{group}/create' => [['GET', 'POST'], 'Admin/FieldOptions/Create'],
    '/{admin}/field-options/{group}/update/{id}' => [['GET', 'POST'], 'Admin/FieldOptions/Update'],
    '/{admin}/field-options/{group}/delete/{id}' => ['GET', 'Admin/FieldOptions/Delete'],

    '/{admin}/languages' => [['GET', 'POST'], 'Admin/Languages/Index'],
    '/{admin}/languages/create' => [['GET', 'POST'], 'Admin/Languages/Create'],
    '/{admin}/languages/update/{id}' => [['GET', 'POST'], 'Admin/Languages/Update'],
    '/{admin}/languages/delete/{id}' => ['GET', 'Admin/Languages/Delete'],

    '/{admin}/categories/{type}' => [['GET', 'POST'], 'Admin/Categories/Index'],
    '/{admin}/categories/{type}/create' => [['GET', 'POST'], 'Admin/Categories/Create'],
    '/{admin}/categories/{type}/create-multiple' => [['GET', 'POST'], 'Admin/Categories/CreateMultiple'],
    '/{admin}/categories/{type}/update/{id}' => [['GET', 'POST'], 'Admin/Categories/Update'],
    '/{admin}/categories/{type}/delete/{id}' => ['GET', 'Admin/Categories/Delete'],

    '/{admin}/locations' => [['GET', 'POST'], 'Admin/Locations/Index'],
    '/{admin}/locations/create' => [['GET', 'POST'], 'Admin/Locations/Create'],
    '/{admin}/locations/create-multiple' => [['GET', 'POST'], 'Admin/Locations/CreateMultiple'],
    '/{admin}/locations/update/{id}' => [['GET', 'POST'], 'Admin/Locations/Update'],
    '/{admin}/locations/delete/{id}' => ['GET', 'Admin/Locations/Delete'],

    '/{admin}/file-types' => [['GET', 'POST'], 'Admin/FileTypes/Index'],
    '/{admin}/file-types/create' => [['GET', 'POST'], 'Admin/FileTypes/Create'],
    '/{admin}/file-types/update/{id}' => [['GET', 'POST'], 'Admin/FileTypes/Update'],
    '/{admin}/file-types/delete/{id}' => ['GET', 'Admin/FileTypes/Delete'],

    '/{admin}/upload-types' => [['GET', 'POST'], 'Admin/UploadTypes/Index'],
    '/{admin}/upload-types/create' => [['GET', 'POST'], 'Admin/UploadTypes/Create'],
    '/{admin}/upload-types/update/{id}' => [['GET', 'POST'], 'Admin/UploadTypes/Update'],
    '/{admin}/upload-types/delete/{id}' => ['GET', 'Admin/UploadTypes/Delete'],

    '/{admin}/email-queue' => [['GET', 'POST'], 'Admin/EmailQueue/Index'],
    '/{admin}/email-queue/view/{id}' => ['GET', 'Admin/EmailQueue/View'],
    '/{admin}/email-queue/approve/{id}' => ['GET', 'Admin/EmailQueue/Approve'],
    '/{admin}/email-queue/delete/{id}' => ['GET', 'Admin/EmailQueue/Delete'],

    '/{admin}/email-templates' => [['GET', 'POST'], 'Admin/EmailTemplates/Index'],
    '/{admin}/email-templates/create' => [['GET', 'POST'], 'Admin/EmailTemplates/Create'],
    '/{admin}/email-templates/update/{id}' => [['GET', 'POST'], 'Admin/EmailTemplates/Update'],
    '/{admin}/email-templates/delete/{id}' => ['GET', 'Admin/EmailTemplates/Delete'],

    '/{admin}/menu-groups' => ['GET', 'Admin/MenuGroups/Index'],
    '/{admin}/menu-groups/create' => [['GET', 'POST'], 'Admin/MenuGroups/Create'],
    '/{admin}/menu-groups/update/{id}' => [['GET', 'POST'], 'Admin/MenuGroups/Update'],
    '/{admin}/menu-groups/delete/{id}' => ['GET', 'Admin/MenuGroups/Delete'],

    '/{admin}/menu/{group}' => ['GET', 'Admin/Menu/Index'],
    '/{admin}/menu/{group}/create' => [['GET', 'POST'], 'Admin/Menu/Create'],
    '/{admin}/menu/{group}/update/{id}' => [['GET', 'POST'], 'Admin/Menu/Update'],
    '/{admin}/menu/{group}/delete/{id}' => ['GET', 'Admin/Menu/Delete'],
    
    '/{admin}/pages' => [['GET', 'POST'], 'Admin/Pages/Index'],
    '/{admin}/pages/create' => [['GET', 'POST'], 'Admin/Pages/Create'],
    '/{admin}/pages/update/{id}' => [['GET', 'POST'], 'Admin/Pages/Update'],
    '/{admin}/pages/delete/{id}' => ['GET', 'Admin/Pages/Delete'],

    '/{admin}/widgets/{page_id}' => [['GET', 'POST'], 'Admin/Widgets/Index'],
    '/{admin}/widgets/{page_id}/create' => [['GET', 'POST'], 'Admin/Widgets/Create'],
    '/{admin}/widgets/{page_id}/update/{id}' => [['GET', 'POST'], 'Admin/Widgets/Update'],
    '/{admin}/widgets/{page_id}/delete/{id}' => ['GET', 'Admin/Widgets/Delete'],

    '/{admin}/{type}-invoices' => [['GET', 'POST'], 'Admin/Invoices/Index'],
    '/{admin}/{type}-invoices/view/{id}' => [['GET', 'POST'], 'Admin/Invoices/View'],
    '/{admin}/{type}-invoices/pay/{id}' => [['GET', 'POST'], 'Admin/Invoices/Pay'],
    '/{admin}/{type}-invoices/print/{id}' => [['GET', 'POST'], 'Admin/Invoices/Print'],

    '/{admin}/{type}-reviews' => [['GET', 'POST'], 'Admin/Reviews/Index'],
    '/{admin}/{type}-reviews/approve' => [['GET', 'POST'], 'Admin/Reviews/Approve'],
    '/{admin}/{type}-reviews/create' => [['GET', 'POST'], 'Admin/Reviews/Create'],
    '/{admin}/{type}-reviews/update/{id}' => [['GET', 'POST'], 'Admin/Reviews/Update'],
    '/{admin}/{type}-reviews/delete/{id}' => ['GET', 'Admin/Reviews/Delete'],

    '/{admin}/{type}-comments' => ['GET', 'Admin/Comments/Index'],
    '/{admin}/{type}-comments/approve' => [['GET', 'POST'], 'Admin/Comments/Approve'],
    '/{admin}/{type}-comments/create' => [['GET', 'POST'], 'Admin/Comments/Create'],
    '/{admin}/{type}-comments/update/{id}' => [['GET', 'POST'], 'Admin/Comments/Update'],
    '/{admin}/{type}-comments/delete/{id}' => ['GET', 'Admin/Comments/Delete'],

    '/{admin}/{type}-messages' => [['GET', 'POST'], 'Admin/Messages/Index'],
    '/{admin}/{type}-messages/approve' => [['GET', 'POST'], 'Admin/Messages/Approve'],
    '/{admin}/{type}-messages/create' => [['GET', 'POST'], 'Admin/Messages/Create'],
    '/{admin}/{type}-messages/update/{id}' => [['GET', 'POST'], 'Admin/Messages/Update'],
    '/{admin}/{type}-messages/delete/{id}' => ['GET', 'Admin/Messages/Delete'],

    '/{admin}/{type}-replies' => [['GET', 'POST'], 'Admin/Replies/Index'],
    '/{admin}/{type}-replies/approve' => [['GET', 'POST'], 'Admin/Replies/Approve'],
    '/{admin}/{type}-replies/create' => [['GET', 'POST'], 'Admin/Replies/Create'],
    '/{admin}/{type}-replies/update/{id}' => [['GET', 'POST'], 'Admin/Replies/Update'],
    '/{admin}/{type}-replies/delete/{id}' => ['GET', 'Admin/Replies/Delete'],

    '/{admin}/{type}-badges' => [['GET', 'POST'], 'Admin/Badges/Index'],
    '/{admin}/{type}-badges/create' => [['GET', 'POST'], 'Admin/Badges/Create'],
    '/{admin}/{type}-badges/update/{id}' => [['GET', 'POST'], 'Admin/Badges/Update'],
    '/{admin}/{type}-badges/delete/{id}' => ['GET', 'Admin/Badges/Delete'],

    '/{admin}/{type}-claims' => [['GET', 'POST'], 'Admin/Claims/Index'],
    '/{admin}/{type}-claims/update/{id}' => [['GET', 'POST'], 'Admin/Claims/Update'],
    '/{admin}/{type}-claims/approve/{id}' => ['GET', 'Admin/Claims/Approve'],
    '/{admin}/{type}-claims/reject/{id}' => ['GET', 'Admin/Claims/Reject'],

    '/{admin}/rating-categories' => [['GET', 'POST'], 'Admin/Ratings/Index'],
    '/{admin}/rating-categories/create' => [['GET', 'POST'], 'Admin/Ratings/Create'],
    '/{admin}/rating-categories/update/{id}' => [['GET', 'POST'], 'Admin/Ratings/Update'],
    '/{admin}/rating-categories/delete/{id}' => [['GET', 'POST'], 'Admin/Ratings/Delete'],

    '/{admin}/tax-rates' => [['GET', 'POST'], 'Admin/Taxes/Index'],
    '/{admin}/tax-rates/create' => [['GET', 'POST'], 'Admin/Taxes/Create'],
    '/{admin}/tax-rates/update/{id}' => [['GET', 'POST'], 'Admin/Taxes/Update'],
    '/{admin}/tax-rates/delete/{id}' => [['GET', 'POST'], 'Admin/Taxes/Delete'],

    '/{admin}/tasks' => [['GET', 'POST'], 'Admin/Tasks/Index'],

    '[/{slug}[/{first}[/{second}]]]' => [['GET', 'POST'], 'Index/Router'],
];

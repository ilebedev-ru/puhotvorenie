$(function() {
    // $('.breadcrumb ol li:after').css('content', $pwmicrobreadcrumbspipe);
    $('body').append('<style type="text/css">.breadcrumb ol li::after{content: \'' + $pwmicrobreadcrumbspipe + '\';}</style>');
});
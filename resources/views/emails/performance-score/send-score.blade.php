@php
$css = [
    'good' => 'green;',
    'fair' => 'orange;',
    'poor' => 'red;',
];


$pageSpeedAudit = \App\Models\PageSpeedAudit::find(1);
$webhook = \App\Models\WebhookData::query()
            ->join('page_speed_audit_webhook_data', 'page_speed_audit_webhook_data.webhook_data_id', '=', 'webhook_data.id')
            ->where('page_speed_audit_webhook_data.page_speed_audit_id', 1)
            ->orderBy('page_speed_audit_webhook_data.webhook_data_id', 'DESC')
            ->first();

$body = json_decode($webhook->body, true);

$ordersPerMonth = $body['orders_per_month'] ?: 1000;
$averageOrderValue = $body['average_order_value'] ?: 50;

$lcpDisplayValue =  $pageSpeedAudit->data_normalized['breakdown']['firstContentfulPaint']['displayValue'];
$lcpNumericValue =  (float) $pageSpeedAudit->data_normalized['breakdown']['firstContentfulPaint']['numericValue'] / 1000;

$lcpDisplayValue =  $pageSpeedAudit->data_normalized['breakdown']['largestContentfulPaint']['displayValue'];
$lcpNumericValue =  (float) $pageSpeedAudit->data_normalized['breakdown']['largestContentfulPaint']['numericValue'] / 1000;

$pageSpeedScore = number_format($pageSpeedAudit->score * 100);
$pageSpeedGrade = 'A';
if ( $pageSpeedScore >= 90 ) {
    $pageSpeedGrade = 'A';
} elseif ( $pageSpeedScore >= 76 && $pageSpeedScore < 90 ) {
    $pageSpeedGrade = 'B';
} elseif ( $pageSpeedScore >= 63 && $pageSpeedScore < 76 ) {
    $pageSpeedGrade = 'C';
} elseif ( $pageSpeedScore >= 50 && $pageSpeedScore < 63 ) {
    $pageSpeedGrade = 'D';
} elseif ( $pageSpeedScore < 50 ) {
    $pageSpeedGrade = 'F';
}

$lcpColor = $css['good'];
if ( $lcpNumericValue > 2.5 && $lcpNumericValue <= 4 ) {
    $lcpColor = $css['fair'];
} elseif ( $lcpNumericValue > 5 ) {
    $lcpColor = $css['poor'];
}


(float) $conversionRate = $bestConversionRate = 3.05;
if ( $lcpNumericValue > 1 && $lcpNumericValue <= 2.4 ) {
    $conversionRate = 1.9;
    // $pageSpeedGrade = 'A';
} elseif ( $lcpNumericValue > 2.4 && $lcpNumericValue <= 3.3) {
    $conversionRate = 1.5;
    // $pageSpeedGrade = 'B';
} elseif ( $lcpNumericValue > 3.3 && $lcpNumericValue <= 4.2) {
    $conversionRate = 0.95;
    // $pageSpeedGrade = 'C';
} elseif ( $lcpNumericValue > 4.2 && $lcpNumericValue <= 5) {
    $conversionRate = 0.6;
    // $pageSpeedGrade = 'D';
} else {
    // $conversionRate = 0.6 - ((( $lcpNumericValue - 5 ) * 0.02) * 0.6);
    $conversionRate = 0.6 - ((( $lcpNumericValue - 5 ) * 0.0442) * 0.6);
    // $pageSpeedGrade = 'F';
}

// if ( $lcpNumericValue > 1 && $lcpNumericValue <= 2 ) {
//     $conversionRate = 1.28;
// } elseif ( $lcpNumericValue > 2 && $lcpNumericValue <= 3 ) {
//     $conversionRate = 1.12;
// } elseif ( $lcpNumericValue > 3 && $lcpNumericValue <= 4 ) {
//     $conversionRate = 0.67;
// } elseif ( $lcpNumericValue > 4 ) {
//     // $conversionRate = 0.67 - ((( $lcpNumericValue - 5 ) * 0.003) * 0.67);
//     $conversionRate = 0.67 - ((( $lcpNumericValue - 5 ) * 0.0442) * 0.67);
// }

$bestCaseRevenue = ($ordersPerMonth * $averageOrderValue) * ($bestConversionRate/100);
$estimatedRevenue = ($ordersPerMonth * $averageOrderValue) * $conversionRate/100;
$difference = ($bestCaseRevenue - $estimatedRevenue);

@endphp

@extends('layouts.email')

@section('content')

<div class="u-row-container" style="padding: 0px;background-color: transparent">
    <div class="u-row" style="Margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #ffffff;">
        <div style="border-collapse: collapse;display: table;width: 100%;background-color: transparent;">
            <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding: 0px;background-color: transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px;"><tr style="background-color: #ffffff;"><![endif]-->

            <!--[if (mso)|(IE)]><td align="center" width="600" style="width: 600px;padding: 0px;" valign="top"><![endif]-->
            <div class="u-col u-col-100"
                style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                <div style="width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                    <!--[if (!mso)&(!IE)]><!-->
                    <div style="padding: 0px;">
                    <!--<![endif]-->

                        <table style="font-family:Arial, Helvetica, sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                            <tbody>
                                <tr>
                                    <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word;padding:0px 0px 10px;font-family:Arial, Helvetica, sans-serif;"align="left">
                                        <table height="0px" align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;border-top: 4px solid #131022;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%">
                                            <tbody>
                                                <tr style="vertical-align: top">
                                                    <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top;font-size: 0px;line-height: 0px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%">
                                                        <span>&#160;</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        {{-- Grade --}}
                        <table id="u_content_text_pagespeed_score" style="font-family:Arial, Helvetica, sans-serif; margin-bottom: 50px;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                            <tbody>
                                <tr>
                                    <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word; padding:10px 50px 0 40px;font-family:Arial, Helvetica, sans-serif;" align="center">
                                        <div style="font-size: 27px; text-align:center; margin: 15px 0; font-weight:bold;">
                                            Performance Impact Score<br>
                                            <span style="font-size: 20px">&mdash; Mobile &mdash;</span>
                                        </div>

                                        <div style="border-radius:100%;height:150px;width:150px; /*border-color:{{ $lcpColor }}; border-width: 10px; border-style:solid'*/ background-color:{{ $lcpColor }}">
                                            <table width="100" height="100" align="center">
                                                <tbody>
                                                    <td valign="middle" height="100%" style="text-align:center; /*color:{{ $lcpColor }};*/ color: #FFFFFF; font-size:90px; line-height: 150px; font-weight: bold;">
                                                        <div>
                                                            {{ $pageSpeedGrade }}
                                                        </div>
                                                    </td>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        {{-- Revenue --}}
                        <table id="u_content_image_2" style="font-family:Arial, Helvetica, sans-serif; background-color: {{ $lcpColor }}; padding-top: 20px;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                            <tbody>
                                <tr>
                                    <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word;padding:25px 0 0 0;font-family:Arial, Helvetica, sans-serif;" align="left">
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td class="v-text-align" style="padding-right: 0px; padding-left: 0px;" align="center">
                                                    <h1 class="v-text-align v-font-size" style="margin: 0px; word-wrap: break-word; font-weight: bold; font-family: 'Raleway',sans-serif; font-size: 27px; color: #FFFFFF">
                                                        Performance Impact on Revenue
                                                        {{-- <br>
                                                        <span style="font-size: 20px">&mdash; Mobile &mdash;</span> --}}
                                                    </h1>
                                                </td>
                                            </tr>
                                    </td>
                                </tr>
                            </tbody>
                        </table>


                        <table id="u_content_image_2" style="font-family:Arial, Helvetica, sans-serif; background-color: {{ $lcpColor }};" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                            <tbody>
                                <tr>
                                    <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word;padding:25px 0 0 0;font-family:Arial, Helvetica, sans-serif;" align="left">
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                            {{-- <tr>
                                                <td class="v-text-align" style="padding-right: 0px; padding-left: 0px;" align="center">
                                                    <div style="{{ ($estimatedRevenue < $bestCaseRevenue ? 'color:red;' : '' )  }} font-size: 75px; font-weight: bold;">
                                                        ${{ number_format($difference) }} <small style="font-size:16px;">missed this month</small>
                                                    </div>
                                                </td>
                                            </tr> --}}

                                            <tr>
                                                <td class="v-text-align" style="padding-right: 0px; padding-left: 0px;" align="center">
                                                    <table width="100%" cellpadding="10" cellspacing="10" border="0" align="center">
                                                        <tbody>
                                                            <tr>
                                                                <td align="right" style="font-weight: bold; font-size:30px; padding-bottom: 10px;  color: #FFFFFF"">
                                                                    ${{ number_format($bestCaseRevenue) }}
                                                                </td>
                                                                <td style=" vertical-align:middle; padding-bottom: 10px; color: #FFFFFF">
                                                                    <span style="border-bottom: 1px solid #FFFFFF;">Optimized</span> Revenue Potential
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td align="right" style="font-weight: bold; font-size:30px;  color: #FFFFFF"">
                                                                    ${{ number_format($estimatedRevenue) }}
                                                                </td>
                                                                <td style=" vertical-align:middle; color: #FFFFFF">
                                                                    <span style="border-bottom: 1px solid #FFFFFF;">Your</span> Performance Revenue
                                                                </td>
                                                            </tr>
                                                            <tr style="background-color: {{ $lcpColor }};">
                                                                <td style="font-size: 75px; font-weight: bold; padding: 10px 0 25px 0; color: #FFFFFF" align="right">
                                                                    ${{ number_format($difference) }}
                                                                </td>
                                                                <td style=" vertical-align:middle; color: #FFFFFF;">
                                                                    <span style="border-bottom: 1px solid #FFFFFF;">Missed Revenue this Month</span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>

                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!--[if (!mso)&(!IE)]><!-->
                    </div>
                    <!--<![endif]-->
                </div>
            </div>
            <!--[if (mso)|(IE)]></td><![endif]-->
            <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
        </div>
    </div>
</div>



{{-- Breakdown --}}
<div class="u-row-container" style="padding: 0px;background-color: transparent">
    <div class="u-row" style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #f5f5f5;">
        <div
            style="border-collapse: collapse;display: table;width: 100%;background-color: transparent;">
            <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding: 0px;background-color: transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px;"><tr style="background-color: #f5f5f5;"><![endif]-->

            <!--[if (mso)|(IE)]><td align="center" width="598" style="width: 598px;padding: 0px;" valign="top"><![endif]-->
            <div class="u-col u-col-100"
                style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                <div style="width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                    <!--[if (!mso)&(!IE)]><!-->
                    <div style="padding: 0px;">
                    <!--<![endif]-->

                        {{-- <table id="u_content_text_pagespeed_score" style="font-family:Arial, Helvetica, sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                            <tbody>
                                <tr>
                                    <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word; padding:40px 50px 0 40px;font-family:Arial, Helvetica, sans-serif;" align="center">
                                        <div style="font-size: 27px; text-align:center; margin: 15px 0; font-weight:bold;">
                                            Performance Impact Score
                                        </div>

                                        <div style="border-radius:100%;height:150px;width:150px; /*border-color:{{ $lcpColor }}; border-width: 10px; border-style:solid'*/ background-color:{{ $lcpColor }}">
                                            <table width="100" height="100" align="center">
                                                <tbody>
                                                    <td valign="middle" height="100%" style="text-align:center; /*color:{{ $lcpColor }};*/ color: #FFFFFF; font-size:90px; line-height: 150px; font-weight: bold;">
                                                        <div>
                                                            {{ $pageSpeedGrade }}
                                                        </div>
                                                    </td>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table> --}}

                        <table id="u_content_text_2" style="font-family:Arial, Helvetica, sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                            <tbody>
                                <tr>
                                    <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word; padding:40px 50px 0 40px;font-family:Arial, Helvetica, sans-serif;" align="left">

                                        <div class="v-text-align" style="color: #5c5c5c; line-height: 170%; text-align: left; word-wrap: break-word;">
                                            <p style="font-size: 20px; text-align:center; margin: 15px 0;">
                                                Time it takes for your main content to load:
                                            </p>

                                             <p style="font-size:50px; text-align:center; color:{{ $lcpColor }}">
                                                <strong>
                                                    {{ $lcpDisplayValue }}
                                                </strong>
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <table id="u_content_text_3" style="font-family:Arial, Helvetica, sans-serif; margin-bottom: 25px;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                            <tbody>
                                <tr>
                                    <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word; padding:40px 50px 0 40px;font-family:Arial, Helvetica, sans-serif;" align="left">

                                        <div class="v-text-align" style="color: #5c5c5c; line-height: 170%; text-align: left; word-wrap: break-word;">
                                            <p style="font-size: 20px; text-align:center; margin: 15px 0;">
                                                Your conversion rate based on {{ $lcpDisplayValue }} load time:
                                            </p>

                                             <p style="font-size:50px; text-align:center; color:{{ $lcpColor }}">
                                                <strong>
                                                    {{ number_format($conversionRate, 2) }}%
                                                </strong>
                                            </p>
                                        </div>

                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <table id="u_content_text_3" style="font-family:Arial, Helvetica, sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                            <tbody>
                                <tr>
                                    <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word; padding:40px 50px 0 40px;font-family:Arial, Helvetica, sans-serif;" align="left">

                                        <div class="v-text-align" style="color: #5c5c5c; line-height: 170%; text-align: left; word-wrap: break-word;">
                                             <p style="font-size:18px; text-align:center;">
                                                <span style="font-weight: bold">Slow load times lead to fewer sales!</span>
                                            </p>

                                            <p style="font-size:18px; text-align:center;">
                                                Conversion Rate is one of the KPIs you should track and constantly work to improve.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <table id="u_content_text_4" style="font-family:Arial, Helvetica, sans-serif; margin-bottom: 50px;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                            <tbody>
                                <tr>
                                    <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word; padding:40px 50px 0 40px;font-family:Arial, Helvetica, sans-serif;" align="left">

                                        <div class="v-text-align" style="color: #5c5c5c; line-height: 170%; text-align: left; word-wrap: break-word;">
                                             <p style="font-size:18px; text-align:center;">
                                                <span style="font-weight: bold">Your Organic SEO will improve with a faster site!</span>
                                            </p>

                                            <p style="font-size:18px; text-align:center;">
                                                 Page load speed is a ranking factor for Google search results.
                                            </p>
                                        </div>

                                    </td>
                                </tr>
                            </tbody>
                        </table>



                        <!--[if (!mso)&(!IE)]><!-->
                    </div>
                    <!--<![endif]-->
                </div>
            </div>
            <!--[if (mso)|(IE)]></td><![endif]-->
            <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
        </div>
    </div>
</div>
{{-- Breakdown --}}

{{-- BEGIN: CTA --}}
<div class="u-row-container" style="padding: 0px;background-color: transparent">
    <div class="u-row"
        style="Margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #131022;">
        <div style="border-collapse: collapse;display: table;width: 100%;background-color: transparent;">
            <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding: 0px;background-color: transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px;"><tr style="background-color: #131022;"><![endif]-->

            <!--[if (mso)|(IE)]><td align="center" width="600" style="width: 600px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;" valign="top"><![endif]-->
            <div class="u-col u-col-100" style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                <div style="width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                    <!--[if (!mso)&(!IE)]><!-->
                    <div style="padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                        <!--<![endif]-->

                        <table id="u_content_button_1" style="font-family:Arial, Helvetica, sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                            <tbody>
                                <tr>
                                    <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word; padding:35px 35px 15px 35px;font-family:Arial, Helvetica, sans-serif; color: #FFFFFF" align="center">
                                        <div class="v-text-align" align="center">
                                            <p style="font-size: 20px;">
                                                Stop leaving money on the table.  Schedule a time to find out how you can increase your sales with a Performance Impact Assessment.
                                            </p>
                                        </div>

                                    </td>
                                </tr>
                                <tr>
                                    <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word;padding:15px 10px 35px 10px; font-family:Arial, Helvetica, sans-serif;" align="left">
                                        <div class="v-text-align" align="center">
                                            <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-spacing: 0; border-collapse: collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;font-family:Arial, Helvetica, sans-serif;"><tr><td class="v-text-align" style="font-family:Arial, Helvetica, sans-serif;" align="center"><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="https://unlayer.com" style="height:50px; v-text-anchor:middle; width:267px;" arcsize="0%" strokecolor="#ffffff" strokeweight="3px" fillcolor="#131022"><w:anchorlock/><center style="color:#FFFFFF;font-family:Arial, Helvetica, sans-serif;"><![endif]-->
                                            <a href="https://savvycal.com/chuckbrockman/performance-impact" target="_blank"
                                                class="v-size-width"
                                                style="box-sizing: border-box;display: inline-block;font-family:Arial, Helvetica, sans-serif;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;color: #FFFFFF; background-color: #131022; border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px; width:47%; max-width:100%; overflow-wrap: break-word; word-break: break-word; word-wrap:break-word; mso-border-alt: none;border-top-color: #ffffff; border-top-style: solid; border-top-width: 3px; border-left-color: #ffffff; border-left-style: solid; border-left-width: 3px; border-right-color: #ffffff; border-right-style: solid; border-right-width: 3px; border-bottom-color: #ffffff; border-bottom-style: solid; border-bottom-width: 3px;">
                                                <span style="display:block;padding:16px 20px;line-height:120%;">
                                                    <strong>
                                                        <span style="font-size: 16px; line-height: 19.2px; font-family: Raleway, sans-serif;">
                                                            Let's Talk!
                                                        </span>
                                                    </strong>
                                                </span>
                                            </a>
                                            <!--[if mso]></center></v:roundrect></td></tr></table><![endif]-->
                                        </div>

                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!--[if (!mso)&(!IE)]><!-->
                    </div>
                    <!--<![endif]-->
                </div>
            </div>
            <!--[if (mso)|(IE)]></td><![endif]-->
            <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
        </div>
    </div>
</div>
{{-- END: CTA --}}


{{-- <div class="u-row-container" style="padding: 0px;background-color: transparent">
    <div class="u-row" style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #f5f5f5;">
        <div
            style="border-collapse: collapse;display: table;width: 100%;background-color: transparent;">
            <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding: 0px;background-color: transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px;"><tr style="background-color: #f5f5f5;"><![endif]-->

            <!--[if (mso)|(IE)]><td align="center" width="598" style="width: 598px;padding: 0px;" valign="top"><![endif]-->
            <div class="u-col u-col-100"
                style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                <div style="width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                    <!--[if (!mso)&(!IE)]><!-->
                    <div style="padding: 0px;b">
                        <!--<![endif]-->

                        <table id="u_content_text_2" style="font-family:Arial, Helvetica, sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                            <tbody>
                                <tr>
                                    <td class="v-container-padding-padding" style="overflow-wrap:break-word;word-break:break-word;padding:40px 50px 50px;font-family:Arial, Helvetica, sans-serif;" align="left">

                                        <div class="v-text-align" style="color: #5c5c5c; line-height: 170%; text-align: left; word-wrap: break-word;">

                                            <p style="font-size:30px; text-align:center; {{ $lcpColor }}">
                                                <strong>
                                                    {{ $lcpDisplayValue }}
                                                </strong>
                                            </p>

                                            <p style="font-size: 14px; line-height: 1.5; margin: 15px 0;">
                                                Cacluations are based on {{ $ordersPerMonth }} orders per month with an average value of ${{ $averageOrderValue }}.
                                            </p>

                                            <p style="font-size: 14px; line-height: 1.5; margin: 15px 0;">
                                                A 1 s page load has an average conversion rate of {{ $bestConversionRate }}%, producing ${{ $bestCaseRevenue }} in revenue.  Based on your page load time, revenue for your pageis estimated to be ${{ $estimatedRevenue }}.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!--[if (!mso)&(!IE)]><!-->
                    </div>
                    <!--<![endif]-->
                </div>
            </div>
            <!--[if (mso)|(IE)]></td><![endif]-->
            <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
        </div>
    </div>
</div> --}}


@endsection

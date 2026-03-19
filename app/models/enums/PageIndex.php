<?php

namespace app\models\enums;

/**
 * Every page that uses the View::render needs a unique index to match it to the correct page in the database.
 * The indices for these pages are defined in this enum.
 *
 * If you need to add a page, add a new name with a UNIQUE index in the right category.
 */
enum PageIndex: int
{
    // 000 - 099 general pages
    case LandingPage = 000;
    case SettingsPage = 001;
    case LoginPage = 002;
    case SignupPage = 003;
    case AttributionPage = 004;
    case ForgotPasswordPage = 005;
    case ResetPasswordPage = 006;

    // 100 - 199 Dance
    case DanceHome = 100;
    case DanceDetail = 101;

    // 200 - 299 History
    case HistoryHome = 200;

    // 300 - 399 Jazz
    case JazzHome = 300;
    case JazzDetail = 301;

    // 400 - 499 Yummy
    case YummyHome = 400;

    // 500 - 599 Admin
    case AdminDashboard = 500;

    // 600 - 699 Misc
    case Error404 = 600;
    case Error405 = 601;

    // 700 - 799 Payment
    case PaymentHome = 700;
    case PaymentCheckout = 701;
}
<?php
namespace App\Libraries;

class HoiAjaxCall {
    // AJAX ACTION
    const AJAX_ADD_WEEKLY_SESSIONS    = 'AJAX_ADD_WEEKLY_SESSIONS';
    const AJAX_UPDATE_WEEKLY_SESSIONS = 'AJAX_UPDATE_WEEKLY_SESSIONS';
    const AJAX_DELETE_WEEKLY_SESSIONS = 'AJAX_DELETE_WEEKLY_SESSIONS';
    const AJAX_UPDATE_SESSIONS        = 'AJAX_UPDATE_SESSIONS';
    const AJAX_UPDATE_BUFFER          = 'AJAX_UPDATE_BUFFER';
    const AJAX_UPDATE_NOTIFICATION    = 'AJAX_UPDATE_NOTIFICATION';
    const AJAX_UPDATE_SETTINGS        = 'AJAX_UPDATE_SETTINGS';
    const AJAX_UPDATE_DEPOSIT         = 'AJAX_UPDATE_DEPOSIT';
    const AJAX_UPDATE_RESERVATIONS    = 'AJAX_UPDATE_RESERVATIONS';
    const AJAX_REFETCHING_DATA        = 'AJAX_REFETCHING_DATA';
    const AJAX_UPDATE_SCOPE_OUTLET_ID = 'AJAX_UPDATE_SCOPE_OUTLET_ID';


//AJAX MSG
    const AJAX_UNKNOWN_CASE                   = 'AJAX_UNKNOWN_CASE';
    const AJAX_UPDATE_WEEKLY_SESSIONS_SUCCESS = 'AJAX_UPDATE_WEEKLY_SESSIONS_SUCCESS';
    const AJAX_UPDATE_WEEKLY_SESSIONS_ERROR   = 'AJAX_UPDATE_WEEKLY_SESSIONS_ERROR';
    const AJAX_UPDATE_SESSIONS_SUCCESS        = 'AJAX_UPDATE_SESSIONS_SUCCESS';
    const AJAX_UPDATE_SCOPE_OUTLET_ID_SUCCESS = 'AJAX_UPDATE_SCOPE_OUTLET_ID_SUCCESS';

    const AJAX_SUCCESS  = 'AJAX_SUCCESS';
    const AJAX_ERROR    = 'AJAX_ERROR';

    const AJAX_VALIDATE_FAIL = 'AJAX_VALIDATE_FAIL';

    const AJAX_UPDATE_SCOPE_OUTLET_ID_ERROR = 'AJAX_UPDATE_SCOPE_OUTLET_ID_ERROR';
    const AJAX_REFETCHING_DATA_SUCCESS = 'AJAX_REFETCHING_DATA_SUCCESS';

    const AJAX_SEARCH_AVAILABLE_TIME  = 'AJAX_SEARCH_AVAILABLE_TIME';
    const AJAX_SUBMIT_BOOKING         = 'AJAX_SUBMIT_BOOKING';
    const AJAX_EDIT_RESERVATION       = 'AJAX_EDIT_RESERVATION';
    
    const AJAX_BOOKING_CONDITION_VALIDATE_FAIL = 'AJAX_BOOKING_CONDITION_VALIDATE_FAIL';
    
    const AJAX_RESERVATION_CUSTOMER_BOOK_TWICE = 'AJAX_RESERVATION_CUSTOMER_BOOK_TWICE';

    const AJAX_AVAILABLE_TIME_FOUND = 'AJAX_AVAILABLE_TIME_FOUND';
    const AJAX_RESERVATION_VALIDATE_FAIL = 'AJAX_RESERVATION_VALIDATE_FAIL';
    const AJAX_RESERVATION_NO_LONGER_AVAILABLE = 'AJAX_RESERVATION_NO_LONGER_AVAILABLE';
    const AJAX_RESERVATION_REQUIRED_DEPOSIT = 'AJAX_RESERVATION_REQUIRED_DEPOSIT';
    const AJAX_RESERVATION_SUCCESS_CREATE = 'AJAX_RESERVATION_SUCCESS_CREATE';
    const AJAX_CONFIRM_RESERVATION = 'AJAX_CONFIRM_RESERVATION';
    const AJAX_CONFIRM_RESERVATION_SUCCESS = 'AJAX_CONFIRM_RESERVATION_SUCCESS';
    const AJAX_RESERVATION_STILL_NOT_RESERVED = 'AJAX_RESERVATION_STILL_NOT_RESERVED';
    
    const AJAX_ALL_OUTLETS = 'AJAX_ALL_OUTLETS';
    
    const AJAX_FETCH_RESERVATIONS_BY_DAY = 'AJAX_FETCH_RESERVATIONS_BY_DAY';
    const AJAX_FETCH_RESERVATIONS_BY_DAY_SUCCESS = 'AJAX_FETCH_RESERVATIONS_BY_DAY_SUCCESS';

    const AJAX_FETCH_RESERVATIONS_BY_RANGE_DAY = 'AJAX_FETCH_RESERVATIONS_BY_RANGE_DAY';
    const AJAX_FETCH_RESERVATIONS_BY_RANGE_DAY_SUCCESS = 'AJAX_FETCH_RESERVATIONS_BY_DAY_SUCCESS';

    
    
    const AJAX_PAYMENT_REQUEST = 'AJAX_PAYMENT_REQUEST';
    
    const AJAX_PAYMENT_REQUEST_VALIDATE_FAIL = 'AJAX_PAYMENT_REQUEST_VALIDATE_FAIL';
    
    const AJAX_PAYMENT_REQUEST_FIND_RESERVATION_FAIL = 'AJAX_PAYMENT_REQUEST_FIND_RESERVATION_FAIL';
    
    const AJAX_PAYMENT_REQUEST_RESERVATION_ALREADY_PAID = 'AJAX_PAYMENT_REQUEST_RESERVATION_ALREADY_PAID';
    
    const AJAX_PAYMENT_REQUEST_TRANSACTION_FAIL = 'AJAX_PAYMENT_REQUEST_TRANSACTION_FAIL';
    
    const AJAX_PAYMENT_REQUEST_SUCCESS = 'AJAX_PAYMENT_REQUEST_SUCCESS';
    
    const SERVER_THROWN_EXCEPTION = 'SERVER_THROWN_EXCEPTION';
    
    const DONT_HAVE_PERMISSION = 'DONT_HAVE_PERMISSION';
    
    const AJAX_FIND_RESERVATION = 'AJAX_FIND_RESERVATION';
    
    const AJAX_FIND_RESERVATION_SUCCESS = 'AJAX_FIND_RESERVATION_SUCCESS';
    
    // Different form customer side self booking
    // This create from admin inside reservations page
    // No check on payment authorization
    const AJAX_CREATE_NEW_RESERVATION = 'AJAX_CREATE_NEW_RESERVATION';

    const AJAX_LOGIN_SUCCESS = 'AJAX_LOGIN_SUCCESS';
    const AJAX_LOGIN_FAIL = 'AJAX_LOGIN_FAIL';
    
    const AJAX_LOGIN = 'AJAX_LOGIN';
    const AJAX_LOGOUT = 'AJAX_LOGOUT';
    
    const AJAX_LOGOUT_SUCCESS = 'AJAX_LOGOUT_SUCCESS';
    const AJAX_CANCEL_RESERVATION = 'AJAX_CANCEL_RESERVATION';
    const AJAX_CANCEL_RESERVATION_SUCCESS = 'AJAX_CANCEL_RESERVATION_SUCCESS';

    const AJAX_SEND_REMINDER_SMS_ON_RESERVATION = 'AJAX_SEND_REMINDER_SMS_ON_RESERVATION';
    const AJAX_SEND_REMINDER_SMS_ON_RESERVATION_SUCCESS = 'AJAX_SEND_REMINDER_SMS_ON_RESERVATION_SUCCESS';
    const AJAX_SEND_REMINDER_SMS_ON_RESERVATION_FAIL = 'AJAX_SEND_REMINDER_SMS_ON_RESERVATION_FAIL';
    const AJAX_SEND_REMINDER_SMS_FAIL_BOOKING_NOT_COMPLETE = 'AJAX_SEND_REMINDER_SMS_FAIL_BOOKING_NOT_COMPLETE';
    
    const AJAX_CREATE_CLOSE_SLOT = 'AJAX_CREATE_CLOSE_SLOT';
    const AJAX_CREATE_CLOSE_SLOT_SUCCESS = 'AJAX_CREATE_CLOSE_SLOT_SUCCESS';

    const AJAX_RESEND_PAYMENT_AUTHORIZATION_REQUEST_SMS = "AJAX_RESEND_PAYMENT_AUTHORIZATION_REQUEST_SMS";
    const AJAX_RESEND_PAYMENT_AUTHORIZATION_REQUEST_SMS_SUCCESS = "AJAX_RESEND_PAYMENT_AUTHORIZATION_REQUEST_SMS_SUCCESS";
    
    const AJAX_FIND_CUSTOMER_SAME_PHONE = "AJAX_FIND_CUSTOMER_SAME_PHONE";
    const AJAX_FIND_CUSTOMER_SAME_PHONE_SUCCESS = "AJAX_FIND_CUSTOMER_SAME_PHONE_SUCCESS";
    const AJAX_FIND_CUSTOMER_SAME_PHONE_NOT_FOUND = "AJAX_FIND_CUSTOMER_SAME_PHONE_NOT_FOUND";
    const AJAX_FIND_CUSTOMER_SAME_PHONE_FAIL = "AJAX_FIND_CUSTOMER_SAME_PHONE_FAIL";
}
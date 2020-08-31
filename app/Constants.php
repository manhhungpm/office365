<?php

define('CODE_SUCCESS', 0);
define('CODE_ERROR', 1);
define('CODE_QUERY', 2);

define('RESPONSE_ERROR', 'ERROR');

define('MESSAGE_SUCCESS', 'success');
define('MESSAGE_ERROR', 'error');

define('ROLE_ADMIN', 'Admin');
define('ROLE_RESELLER', 'Reseller');

define('ACCOUNT_STATUS_DEACTIVE', 0);
define('ACCOUNT_STATUS_ACTIVE', 1);

define('STUDENT_STATUS_UNUSED', 0);
define('STUDENT_STATUS_ACTIVE', 1);
define('STUDENT_STATUS_BLOCK', 2);

//Action_log constant
define('LOGIN', 'Login');
define('LOGOUT', 'Logout');
define('ADD', 'Add');
define('UPDATE', 'Update');
define('DELETE', 'Delete');

define('API_TOKEN', 'https://login.microsoftonline.com/?/oauth2/v2.0/token');
define('GRAPH_ROOT', 'https://graph.microsoft.com/v1.0');
define('API_DOMAIN', GRAPH_ROOT . '/domains');
define('API_USER', GRAPH_ROOT . '/users');
define('API_SUBSCRIBED_SKU', GRAPH_ROOT . '/subscribedSkus');
define('API_ASSIGN_LICENSE', API_USER . '/?/assignLicense');

//Code error
define('CODE_ERROR_DELETE_USER_WHEN_HAVE_USER_CREATED_AND_STUDENT_CODE',3);
define('CODE_ERROR_DELETE_STUDENT_CODE_WHEN_HAVE_STATUS_ACTIVE',4);


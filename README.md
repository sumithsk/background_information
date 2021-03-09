About
=====
A Drupal 8/9 supported module which provide ability to add api key
field on site information page (admin/config/system/site-information).
It also creates a route (/page_json/FOOBAR12345/1) where user can see
json data of a node of page content type.

Where:
'FOOBAR12345': is an API key.
1: node id of the page type.

The api key will be used for authenticating the request
and provide node data accordingly.


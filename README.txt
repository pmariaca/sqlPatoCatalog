sqlPatoCatalog

README

sqlPatoCatalog is a catalog where you can save and organize your query statments and run them. As well you can generate reports of the SQL statment result in csv or pdf. 
(images in https://pmariaca.github.io/?p=20221104 )

With this catalog you can:

- Save your queries and order them in groups. These queries are saved in an XML file. Menu customization also have the option to delete some or all of the group consultations.

- See the result of the SQL statment, this is generated in a table for which I use the datatables plugin (https://www.datatables.net/) also allows generating csv / pdf file with the result.

- Open multiple tabs to perform different searches as well as to compare the results I added another entry for SQL queries which has its own set of tabs.

- See the list of tables of the selected database, with a filter to find a particular table, for this I use the Selectr jquery (https://github.com/caseyWebb/selectr).

- Save user configuration for one server.

- You can customize your view with different less of bootstrap, in this moment are 5 views.

--------------------------------------------------------

Tested with Php.5.12 and Php 7.4 with MySqli extension, Mysql 5.6 and 8.0, and Apache or lighttpd server, and in browsers like Firefox and Chrome.

INSTALL
Unzip in some directory

CONFIGURE
The directories files and config need written permission
sudo chown -R www-data.www-data files/
sudo chown -R www-data.www-data config/

@copyright Copyright (c) Patricia Mariaca Hajducek
@license http://opensource.org/licenses/MIT

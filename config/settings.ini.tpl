[db]
dbname = @DBNAME@
host = @DBHOST@
username = @DBUSER@
password = @DBPASS@
port =  ; Can be empty
driver = mysql
; separate options by comma
options = "PDO::MYSQL_ATTR_INIT_COMMAND=set names utf8"

[export]
delimiter = "\t"                    ; leave empty for default comma, use "\t" for tab
separator =                         ; leave empty for default double quote; if tab is used as delimiter, no separator will be used

[credits]
; Credits string appears for each record in each individual text file
string = "@CREDITSSTRING@"
; Release date used in GSD EML files; format as YYYY-MM-DD
release_date = "@RELEASEDATE@"

[webservice]
url = "@WEBSERVICEURL@"

[settings]
version = @APP.VERSION@
revision = @APP.REVISION@

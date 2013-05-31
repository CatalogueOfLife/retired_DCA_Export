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

[website]
url = "@WEBSITEURL@" ; base url to CoL, needed as different editions run simultaneously

[webservice]
url = "@WEBSERVICEURL@"

[settings]
version = @APP.VERSION@
revision = @APP.REVISION@

; Experimental option to exclude specific source databases from appearing in the results
; Option is not set in name of resulting archive, so delete any existing archives before setting this option
; Note that setting this option will have a negative impact on the speed of archive creation
[excluded_source_dbs]
ids = ""    					    	; source database to exclude from results, separated by comma

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
natural_keys = 1				    ; export using natural keys (1) or ids (0) in CoL urls
fossils = 0							; export fossils (1) or exclude them (0)

[website]
url = "@WEBSITEURL@" ; base url to CoL, needed as different editions run simultaneously

[webservice]
url = "@WEBSERVICEURL@"

[settings]
version = @APP.VERSION@
revision = @APP.REVISION@

; Data used to decorate col.xml eml file that is part of every archive
[col_eml]
authorsEditors = "Bisby F., Roskov Y., Culham A., Orrell T., Nicolson D., Paglinawan L., Bailly N., Appeltans W., Kirk P., Bourgoin T., Baillargeon G., Ouvrard D., eds"
abstract =
version =
contact = "support@sp2000.org"
sourceUrl = "http://www.catalogueoflife.org/"
resourceLogoUrl = "images/databases/Species_2000_Common_Names.gif"

; Additional data used to decorate eml.xml meta eml file that is part of every archive
[col_meta_eml]
issn="ISSN 1473-009X"

; Experimental option to exclude specific source databases from appearing in the results
; Option is not set in name of resulting archive, so delete any existing archives before setting this option
; Note that setting this option will have a negative impact on the speed of archive creation
[excluded_source_dbs]
ids = ""    					    	; source database to exclude from results, separated by comma

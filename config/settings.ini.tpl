[db]
dbname = base_scheme_2011_19
host = localhost
username = root
password = root
port =  ; Can be empty
driver = mysql
; separate options by comma
options = "PDO::MYSQL_ATTR_INIT_COMMAND=set names utf8"

[export]
export_dir = export/       ; trailing slash required
delimiter = ";"            ; leave empty for default comma 
separator =                ; leave empty for default double quote

[settings]
version = @APP.VERSION@
revision = @APP.REVISION@
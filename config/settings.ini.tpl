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
delimiter = "\t"                    ; leave empty for default comma, use "\t" for tab
separator =                         ; leave empty for default double quote; if tab is used as delimiter, no separator will be used
export_dir = export/                ; trailing slash required (check if writable)
xml_template = template/meta.tpl    ; template for meta xml file (should not be writable)
zip_archive = zip/archive           ; file name to zip archive to, do not append .zip extension! (check if writable)

[credits]
; Credits string appears for each record in each individual text file
string = "Species 2000 & ITIS Catalogue of Life: 2011 Annual Checklist"

[settings]
version = @APP.VERSION@
revision = @APP.REVISION@
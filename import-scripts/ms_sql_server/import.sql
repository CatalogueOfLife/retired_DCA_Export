-- Modify as appropriate for your own situation!
DECLARE @export_dir VARCHAR(100) = 'C:\Users\Ayco Holleman\Downloads\dca01\';


TRUNCATE TABLE "Taxon";

TRUNCATE TABLE "Distribution";

TRUNCATE TABLE "Reference";

TRUNCATE TABLE "VernacularName";



DECLARE @bulk_cmd varchar(1000);

SET @bulk_cmd = 'BULK INSERT Taxon
FROM ''' + @export_dir + 'taxa.txt'' 
WITH (FIRSTROW = 2, FIELDTERMINATOR = ''' + CHAR(9) + ''', ROWTERMINATOR = ''' + CHAR(10)+ ''')';

SET @bulk_cmd = 'BULK INSERT Distribution
FROM ''' + @export_dir + 'distribution.txt'' 
WITH (FIRSTROW = 2, FIELDTERMINATOR = ''' + CHAR(9) + ''', ROWTERMINATOR = ''' + CHAR(10)+ ''')';

SET @bulk_cmd = 'BULK INSERT VernacularName
FROM ''' + @export_dir + 'vernacular.txt'' 
WITH (FIRSTROW = 2, FIELDTERMINATOR = ''' + CHAR(9) + ''', ROWTERMINATOR = ''' + CHAR(10)+ ''')';

SET @bulk_cmd = 'BULK INSERT Reference
FROM ''' + @export_dir + 'reference.txt'' 
WITH (FIRSTROW = 2, FIELDTERMINATOR = ''' + CHAR(9) + ''', ROWTERMINATOR = ''' + CHAR(10)+ ''')';

EXEC(@bulk_cmd);

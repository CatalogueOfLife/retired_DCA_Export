i4Life WP4 ENHANCED DOWNLOAD SERVICE OF THE CATALOGUE OF LIFE: DARWIN CORE ARCHIVE EXPORT
v1.2, 23-03-12


INTRODUCTION

This application exports the contents of a Catalogue of Life Annual Checklist to Darwin Core Archive format. 
The DCA_export class connects to a Base Scheme database and exports taxon data to text files, 'interconnected'
by the meta.xml XML file. After export, the directory containing the export files is automatically zipped and 
ready for download. See comments in the meta.xml file for the matching between CoL fields and DCA terms.


REQUIREMENTS

Identical to the Annual Checklist 1.6, plus the Zip extension should be enabled. 
Compile PHP 5.2 with zip support by using the --enable-zip configure option.


CONFIGURATION

Configure the application by modifying config/settings.ini. The database should be a base schema database that 
contains the '_search_scientific' and '_source_database_details' denormalized tables. 

Make sure that 'export' and 'zip', the directories used to store the xml and text files, are writable by the web server.
The application automatically creates the 'dataset' directory within the 'export' directory.


USER INTERFACE

The interface is comparable to the taxonomic browser in the Catalogue of Life Annual Checklist. By default, a complete data
set is returned, but the user can restrict this to a 'limited data' set or 'classification only'. The options are explained 
in the interface. After selecting a taxon...

1. creates the meta.xml document from a template,
2. writes the data for each taxon to the appropriate Darwin Core Archive text files in the directory 'export',
3. writes the metadata for each taxon's source database to an GBIF EML file in the subdirectory 'dataset',
4. zips the xml and text files into a downloadable archive in the directory 'zip'.

The zip file is labelled 'archive-[rank]-[taxon]-([rank2]-[taxon2] etc)-bl[block level].zip'.

A hidden is feature is the option to create a complete dump. Use [all] (including square the brackets) as the taxon name.
After the export has completedly, move the zip file to the zip-fixed directory.

If an archive for this configuration is already present on the server, the export is skipped. Instead, the user 
is offered the option to directly download the existing archive.


ADMINISTRATOR OPTIONS

1. The administrator can offer an archive of the complete database by placing this as 'archive-complete.zip' 
   in the directory 'zip-fixed'. If a file with that name exists in 'zip-fixed', a 'download the complete Catalogue of Life'
   link will automatically appear on the index page.
2. Previous editions of complete archives can be offered as well. The naming convention of such files should be
   'yyyy-mm-dd-archive-complete.zip' and these files too should be stored in 'zip-fixed'. If one or more files following the
   convention exist in 'zip-fixed', a 'download a previous edition' link will automatically appear on the index page.



Please submit bugs and suggestions to:
http://dev.4d4life.eu:8081/browse/DS

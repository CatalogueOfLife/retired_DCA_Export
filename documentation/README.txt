i4Life WP4 ENHANCED DOWNLOAD SERVICE OF THE CATALOGUE OF LIFE: DARWIN CORE ARCHIVE EXPORT
v1.0, 08-09-11


INTRODUCTION

This is a demo version of the DCA export application. The DCA_export class connects to a Base Scheme database and 
exports taxa in the DCA format. The application is not yet complete, but the current framework is easily extended 
once the export format is finalized.


REQUIREMENTS

Identical to the Annual Checklist 1.6, plus the Zip extension should be enabled. 
Compile PHP 5.2 with zip support by using the --enable-zip configure option.


CONFIGURATION

Configure the application by modifying config/settings.ini. The database should be a base schema database that 
contains the '_search_scientific' and '_source_database_details' denormalized tables. 

Make sure that 'export' and 'zip', the directories used to store the xml and text files, are writable by the web server.
The application automatically creates the 'dataset' directory within the 'export' directory.


INTERFACE

The demo features a basic interface that allows the user to select all taxa within a given taxon to be selected, 
e.g. all members of a particular family. The user can restrict the amount of data returned by selecting a Block level. The
lower the Block level, the fewer data is created. After entering the rank and taxon name, the application...

1. creates the meta.xml document from a template,
2. writes the data for each taxon to the appropriate Darwin Core Archive text files in the directory 'export',
3. writes the metadata for each taxon's source database to an GBIF EML file in the subdirectory 'dataset',
4. zips the xml and text files into a downloadable archive in the directory 'zip'.

The zip file is labelled 'archive-[rank]-[taxon]-bl[block level].zip'.

If an archive for this configuration is already present on the server, the export is skipped. Instead, the user 
is offered the option to directly download the existing archive.


Please submit bugs and suggestions to:
http://dev.4d4life.eu:8081/browse/DS

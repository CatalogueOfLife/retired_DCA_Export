DARWIN CORE ARCHIVE EXPORT APPLICATION
v0.1, 27-05-11


INTRODUCTION

This is a demo version of the DCA export application. The DCA_export class connects to a Base Scheme database and exports taxa in the DCA format. The application is not yet complete, but the current framework is easily extended once the export format is finalized. Currently, taxa, references and vernacular names are exported; in the future this should be extended with distributions, descriptions and habitats.


REQUIREMENTS

Identical to the Annual Checklist 1.6, plus the Zip extension should be enabled. Compile PHP 5.2 with zip support by using the --enable-zip configure option.


CONFIGURATION

Configure the application by modifying config/settings.ini. The database should be a base schema database that contains the '_search_scientific' denormalized table. Make sure that 'export_dir' and 'zip_archive', the directories used to store the xml and text files, are writable by the server.


INTERFACE

The demo features a very basic interface that allows the user to select all taxa within a given taxon to be selected, e.g. all members of a particular family. After entering the rank and taxon name, the application...

1. creates the meta.xml document from a template,
2. writes the data for each taxon to the appropriate text files,
3. zips the xml and text files into a downloadable archive.

The zip file is labelled 'archive-rank-taxon.zip'.


DEMO-ONLY

As the standard has not yet been finalized, only a subset of the available data is exported in this demo. This obviates the need to rewrite the export classes in the future. When the standard has been fixed, writing the remaining export modules is a matter of a few days.

Due to time constraints, the software has hardly been tested. In particular the creation of very large archives has not yet been tested on all platforms.

Please submit bugs and suggestions to:
http://dev.4d4life.eu:8081/browse/DTPROTO


IMPROVEMENTS

Several potential improvements spring to mind that would make the software easier to use and/or more robust. In no particular order:

1. Add an event listener on the DCA_Export class that will return exceptions, iterator, etc. Currently the application does not yet provide feedback when no results are found.
2. Extend the way search criteria can be passed on to DCA_Export; currently only a single query on rank = 'taxon name' is possible.
3. Less Spartan interface if visitors are allowed to create archives on-the-fly.
4. Skip export if archive file already exists and is up-to-date.

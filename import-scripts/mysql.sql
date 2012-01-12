--
-- Database: `dwc_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `Distribution`
--

CREATE TABLE IF NOT EXISTS `Distribution` (
  `taxonID` int(11) NOT NULL,
  `locationID` varchar(255) default NULL,
  `locality` text,
  `occurrenceStatus` varchar(255) default NULL,
  `establishmentMeans` varchar(255) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Reference`
--

CREATE TABLE IF NOT EXISTS `Reference` (
  `taxonID` int(11) NOT NULL,
  `creator` varchar(255) default NULL,
  `date` varchar(255) default NULL,
  `title` varchar(255) default NULL,
  `description` text,
  `identifier` varchar(255) default NULL,
  `type` varchar(255) default NULL,
  PRIMARY KEY  (`taxonID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `SpeciesProfile`
--

CREATE TABLE IF NOT EXISTS `SpeciesProfile` (
  `taxonID` int(11) NOT NULL,
  `habitat` varchar(255) default NULL,
  PRIMARY KEY  (`taxonID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Taxon`
--

CREATE TABLE IF NOT EXISTS `Taxon` (
  `taxonID` int(11) NOT NULL,
  `identifier` varchar(255) default NULL,
  `datasetID` varchar(255) default NULL,
  `datasetName` varchar(255) default NULL,
  `acceptedNameUsageID` int(11) default NULL,
  `parentNameUsageID` int(11) default NULL,
  `taxonomicStatus` varchar(255) default NULL,
  `taxonRank` varchar(255) default NULL,
  `verbatimTaxonRank` varchar(255) default NULL,
  `scientificName` varchar(255) default NULL,
  `kingdom` varchar(255) default NULL,
  `phylum` varchar(255) default NULL,
  `class` varchar(255) default NULL,
  `order` varchar(255) default NULL,
  `superfamily` varchar(255) default NULL,
  `family` varchar(255) default NULL,
  `genus` varchar(255) default NULL,
  `subgenus` varchar(255) default NULL,
  `specificEpithet` varchar(255) default NULL,
  `infraspecificEpithet` varchar(255) default NULL,
  `scientificNameAuthorship` varchar(255) default NULL,
  `source` text,
  `namePublishedIn` text,
  `nameAccordingTo` varchar(255) default NULL,
  `modified` varchar(255) default NULL,
  `description` text,
  `taxonConceptID` varchar(255) default NULL,
  `scientificNameID` varchar(255) default NULL,
  PRIMARY KEY  (`taxonID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `VernacularName`
--

CREATE TABLE IF NOT EXISTS `VernacularName` (
  `taxonID` int(11) NOT NULL,
  `vernacularName` varchar(255) NOT NULL,
  `language` varchar(255) NOT NULL,
  `countryCode` varchar(255) NOT NULL,
  `locality` varchar(255) NOT NULL,
  `transliteration` varchar(255) NOT NULL,
  PRIMARY KEY  (`taxonID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOAD DATA INFILE '/Users/ruud/Desktop/dwca/export/taxa.txt' REPLACE INTO TABLE `Taxon` IGNORE 1 LINES
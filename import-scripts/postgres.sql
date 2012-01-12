--
-- Database: "dwc_db"
--

-- --------------------------------------------------------

--
-- Table structure for table "Distribution"
--

CREATE TABLE IF NOT EXISTS "Distribution" (
  "taxonID" integer NOT NULL,
  "locationID" character varying,
  "locality" text,
  "occurrenceStatus" character varying,
  "establishmentMeans" character varying default NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table "Reference"
--

CREATE TABLE IF NOT EXISTS "Reference" (
  "taxonID" integer NOT NULL,
  "creator" character varying,
  "date" character varying,
  "title" character varying,
  "description" text,
  "identifier" character varying,
  "type" character varying,
  PRIMARY KEY  ("taxonID")
) ;

-- --------------------------------------------------------

--
-- Table structure for table "SpeciesProfile"
--"

CREATE TABLE IF NOT EXISTS "SpeciesProfile" (
  "taxonID" integer NOT NULL,
  "habitat" character varying,
  PRIMARY KEY  ("taxonID")
) ;

-- --------------------------------------------------------

--
-- Table structure for table "Taxon"
--

CREATE TABLE IF NOT EXISTS "Taxon" (
  "taxonID" integer NOT NULL,
  "identifier" character varying,
  "datasetID" character varying,
  "datasetName" character varying,
  "acceptedNameUsageID" integer,
  "parentNameUsageID" integer,
  "taxonomicStatus" character varying,
  "taxonRank" character varying,
  "verbatimTaxonRank" character varying,
  "scientificName" character varying,
  "kingdom" character varying,
  "phylum" character varying,
  "class" character varying,
  "order" character varying,
  "superfamily" character varying,
  "family" character varying,
  "genus" character varying,
  "subgenus" character varying,
  "specificEpithet" character varying,
  "infraspecificEpithet" character varying,
  "scientificNameAuthorship" character varying,
  "source" text,
  "namePublishedIn" text,
  "nameAccordingTo" character varying,
  "modified" character varying,
  "description" text,
  "taxonConceptID" character varying,
  "scientificNameID" character varying,
  PRIMARY KEY  ("taxonID")
) ;

-- --------------------------------------------------------

--
-- Table structure for table "VernacularName"
--

CREATE TABLE IF NOT EXISTS "VernacularName" (
  "taxonID" integer NOT NULL,
  "vernacularName" character varying NOT NULL,
  "language" character varying NOT NULL,
  "countryCode" character varying NOT NULL,
  "locality" character varying NOT NULL,
  "transliteration" character varying NOT NULL,
  PRIMARY KEY  ("taxonID")
) ;


COPY "Taxon" FROM '/Users/ruud/Desktop/dwca/export/taxa.txt' WITH DELIMITER AS '	' CSV HEADER
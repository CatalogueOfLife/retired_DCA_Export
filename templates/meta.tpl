<archive
 	metadata="eml.xml"
	xmlns='http://rs.tdwg.org/dwc/text/'
    xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'
    xsi:schemaLocation='http://rs.tdwg.org/dwc/text/ http://rs.tdwg.org/dwc/text/tdwg_dwc_text.xsd'>

    <core encoding='UTF-8' fieldsEnclosedBy='[sep]' fieldsTerminatedBy='[del]' linesTerminatedBy='\n' ignoreHeaderLines='1' rowType='http://rs.tdwg.org/dwc/terms/Taxon'>
    <files>
        <location>taxa.txt</location>
    </files>
    <id index='0' />
    <!-- CoL internal id -->
    <field index="0" term="http://rs.tdwg.org/dwc/terms/taxonID"/>
    <!-- CoL "natural key" -->
    <field index="1" term="http://purl.org/dc/terms/identifier"/>
    <!-- CoL source database id -->
    <field index="2" term="http://rs.tdwg.org/dwc/terms/datasetID"/>
    <!-- Short name of source database plus CoL credits -->
    <field index="3" term="http://rs.tdwg.org/dwc/terms/datasetName"/>
    <!-- CoL taxon id of accepted taxon (relevant for synonyms only) -->
    <field index='4' term='http://rs.tdwg.org/dwc/terms/acceptedNameUsageID'/>
    <!-- CoL taxon id of parent taxon (relevant for valid taxa only) -->
    <field index='5' term='http://rs.tdwg.org/dwc/terms/parentNameUsageID'/>
    <!-- Species 2000 status -->
    <field index='6' term='http://rs.tdwg.org/dwc/terms/taxonomicStatus'/>
    <!-- Taxonomic rank -->
    <field index='7' term='http://rs.tdwg.org/dwc/terms/taxonRank'/>
    <!-- Infraspecific marker if displayed in complete scientific name -->
    <field index='8' term='http://rs.tdwg.org/dwc/terms/verbatimTaxonRank'/>
    <!-- Complete scientific name, including subspecific marker where appropriate -->
    <field index='9' term='http://rs.tdwg.org/dwc/terms/scientificName'/>
    <!-- Top level group; listed as kingdom but may be interpreted as domain or superkingdom
         The following eight groups are recognized: Animalia, Archaea, Bacteria, Chromista,
         Fungi, Plantae, Protozoa, Viruses -->
    <field index='10' term='http://rs.tdwg.org/dwc/terms/kingdom'/>
    <!-- Phylum in which the taxon has been classified -->
    <field index='11' term='http://rs.tdwg.org/dwc/terms/phylum'/>
    <!-- Class in which the taxon has been classified -->
    <field index='12' term='http://rs.tdwg.org/dwc/terms/class'/>
    <!-- Order in which the taxon has been classified -->
    <field index='13' term='http://rs.tdwg.org/dwc/terms/order'/>
    <!-- Superfamily in which the taxon has been classified -->
    <field index='14' term='http://rs.tdwg.org/dwc/terms/superfamily'/>
    <!-- Family in which the taxon has been classified -->
    <field index='15' term='http://rs.tdwg.org/dwc/terms/family'/>
    <!-- Genus in which the taxon has been classified -->
    <field index='16' term='http://rs.tdwg.org/dwc/terms/genericName'/>
    <!-- May be different from the above for synonyms only: this field stores the accepted genus name for a synonym -->
    <field index='17' term='http://rs.tdwg.org/dwc/terms/genus'/>
    <!-- Subgenus in which the taxon has been classified -->
    <field index='18' term='http://rs.tdwg.org/dwc/terms/subgenus'/>
    <!-- Specific epithet; for hybrids, the multiplication symbol is included in the epithet -->
    <field index='19' term='http://rs.tdwg.org/dwc/terms/specificEpithet'/>
    <!-- Infraspecific epithet -->
    <field index='20' term='http://rs.tdwg.org/dwc/terms/infraspecificEpithet'/>
    <!-- Authorship -->
    <field index='21' term='http://rs.tdwg.org/dwc/terms/scientificNameAuthorship'/>
    <!-- Acceptance status published in -->
    <field index='22' term='http://purl.org/dc/terms/source'/>
    <!-- Reference in which the scientific name was first published -->
    <field index='23' term='http://rs.tdwg.org/dwc/terms/namePublishedIn'/>
    <!-- Taxon scrutinized by -->
    <field index='24' term='http://rs.tdwg.org/dwc/terms/nameAccordingTo'/>
    <!-- Scrutiny date; may be text string -->
    <field index='25' term='http://purl.org/dc/terms/modified'/>
    <!-- Additional data for the taxon -->
    <field index='26' term='http://purl.org/dc/terms/description'/>
    <!-- Taxonomic concept GUID provided by CoL provider -->
    <field index='27' term='http://rs.tdwg.org/dwc/terms/taxonConceptID'/>
    <!-- Name GUID provided by CoL provider (currently original CoL name code) -->
    <field index='28' term='http://rs.tdwg.org/dwc/terms/scientificNameID'/>
    <!-- Url to the taxon in the current CoL -->
    <field index='29' term='http://purl.org/dc/terms/references'/>
    <!-- Flag to indicate if taxon is extinct -->
    <field index='30' term='http://rs.gbif.org/terms/1.0/isExtinct'/>
    </core>

    <!-- Structured distributions: http://rs.gbif.org/extension/gbif/1.0/distribution.xml -->
    <extension encoding='UTF-8' fieldsEnclosedBy='[sep]' fieldsTerminatedBy='[del]' linesTerminatedBy='\n' ignoreHeaderLines='1' rowType='http://rs.gbif.org/terms/1.0/Distribution'>
        <files>
            <location>distribution.txt</location>
        </files>
        <coreid index='0' />
        <!-- Original id prefixed with the standard, e.g. tdwg:AGE-BA; eez:polish; fao:18, iso3166-1-alhpa-2:SN
             Is left empty in case distribution is taken from free text string -->
        <field index='1' term='http://rs.tdwg.org/dwc/terms/locationID'/>
        <!-- Locality as verbatim string -->
        <field index='2' term='http://rs.tdwg.org/dwc/terms/locality'/>
        <!-- Distribution status (currently not yet implemented, reserved for future edition) -->
        <field index='3' term='http://rs.tdwg.org/dwc/terms/occurrenceStatus'/>
        <!-- The process by which the taxon became established -->
        <field index='4' term='http://rs.tdwg.org/dwc/terms/establishmentMeans'/>
    </extension>

	<!-- Unstructured distributions: http://rs.gbif.org/extension/gbif/1.0/description.xml -->
    <extension encoding='UTF-8' fieldsEnclosedBy='[sep]' fieldsTerminatedBy='[del]' linesTerminatedBy='\n' ignoreHeaderLines='1' rowType='http://rs.gbif.org/terms/1.0/Description'>
		<files>
			<location>description.txt</location>
		</files>
		<coreid index="0" />
		<!-- Fixed to distribution text only -->
		<field default="Distribution" term="http://purl.org/dc/terms/type"/>
		<!-- Full, unstructured text -->
		<field index="1" term="http://purl.org/dc/terms/description"/>
 		<!-- All distributions are in English -->
		<field default="en" term="http://purl.org/dc/terms/language"/>
	</extension>

    <extension encoding='UTF-8' fieldsEnclosedBy='[sep]' fieldsTerminatedBy='[del]' linesTerminatedBy='\n' ignoreHeaderLines='1' rowType='http://rs.gbif.org/terms/1.0/Reference'>
        <files>
            <location>reference.txt</location>
        </files>
        <coreid index='0' />
        <!-- Author(s) -->
        <field index='1' term='http://purl.org/dc/terms/creator'/>
        <!-- Year -->
        <field index='2' term='http://purl.org/dc/terms/date'/>
        <!-- Title -->
        <field index='3' term='http://purl.org/dc/terms/title'/>
        <!-- Published in -->
        <field index='4' term='http://purl.org/dc/terms/source'/>
        <!-- Uri -->
        <field index='5' term='http://purl.org/dc/terms/identifier'/>
        <!-- Type of reference; pertaining to taxon, synonym or vernacular name -->
        <field index='6' term='http://purl.org/dc/terms/type'/>
    </extension>

    <extension encoding='UTF-8' fieldsEnclosedBy='[sep]' fieldsTerminatedBy='[del]' linesTerminatedBy='\n' ignoreHeaderLines='1' rowType='http://rs.gbif.org/terms/1.0/SpeciesProfile'>
         <files>
            <location>speciesprofile.txt</location>
         </files>
         <coreid index='0' />
         <!-- Life zone; these comprise: marine, terrestrial, brackish, freshwater, unknown -->
         <field index='1' term='http://rs.tdwg.org/dwc/terms/habitat' vocabulary='http://www.catalogueoflife.org/dwc/habitats-classification-scheme'/>
    </extension>

    <extension encoding='UTF-8' fieldsEnclosedBy='[sep]' fieldsTerminatedBy='[del]' linesTerminatedBy='\n' ignoreHeaderLines='1' rowType='http://rs.gbif.org/terms/1.0/VernacularName'>
        <files>
            <location>vernacular.txt</location>
        </files>
        <coreid index='0' />
        <!-- Vernacular name -->
        <field index='1' term='http://rs.tdwg.org/dwc/terms/vernacularName'/>
        <!-- Language -->
        <field index='2' term='http://purl.org/dc/terms/language'/>
        <!-- Country in which the vernacular name is used -->
        <field index='3' term='http://rs.tdwg.org/dwc/terms/countryCode'/>
        <!-- Region in which the vernacular name is used -->
        <field index='4' term='http://rs.tdwg.org/dwc/terms/locality'/>
        <!-- Transliteration -->
        <field index='5' term='http://www.catalogueoflife.org/dwc/terms/transliteration'/>
    </extension>

</archive>

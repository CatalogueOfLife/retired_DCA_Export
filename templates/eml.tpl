<eml:eml xmlns:eml="eml://ecoinformatics.org/eml-2.1.1"
	xmlns:md="eml://ecoinformatics.org/methods-2.1.1" 
	xmlns:proj="eml://ecoinformatics.org/project-2.1.1"
	xmlns:d="eml://ecoinformatics.org/dataset-2.1.1" 
	xmlns:res="eml://ecoinformatics.org/resource-2.1.1"
	xmlns:dc="http://purl.org/dc/terms/" 
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="eml://ecoinformatics.org/eml-2.1.1 http://rs.gbif.org/schema/eml-gbif-profile/dev/eml.xsd"
	packageId="[packageId]"
	system="catalogueoflife.org" 
	scope="system" 
	xml:lang="eng">
	<dataset>
		<alternateIdentifier system="catalogueoflife.org">[id]</alternateIdentifier>

		<title xml:lang="eng">[title]</title>
		<shortName>[abbreviatedName]</shortName>
		
		<creator>
			<individualName>
   				<surName>[authorsEditors]</surName>
 			</individualName>
 			<organizationName>[organization]</organizationName>
 			<onlineUrl>[sourceUrl]</onlineUrl>
		</creator>

		<creator>
			<organizationName>Species 2000 &amp; ITIS Catalogue of Life</organizationName>
			<address>
				<city>Leiden</city>
				<postalCode>2332 AA</postalCode>
				<country>NL</country>
			</address>
			<onlineUrl>http://www.catalogueoflife.org</onlineUrl>
		</creator>
		
		<metadataProvider>
			<organizationName>Species 2000 Secretariat</organizationName>
			<address>
				<city>Leiden</city>
				<postalCode>2332 AA</postalCode>
				<country>NL</country>
			</address>
			<onlineUrl>http://www.sp2000.org/</onlineUrl>
		</metadataProvider>

		<associatedParty>
			<individualName>
				<givenName></givenName>
				<surName>[authorsEditors]</surName>					
			</individualName>
			<role>editor</role>
		</associatedParty>
		
		<pubDate>[pubDate]</pubDate>

		<language>eng</language>
	
		<abstract>
			<para>[abstract]</para>
		</abstract>
		
		<distribution scope="document">
			<online>
				<url function="information">[sourceUrl]</url>
			</online>
		</distribution>
		
		<contact>
			<organizationName>Species 2000 Secretariat c/o Naturalis</organizationName>
			<address>
				<deliveryPoint>P.O. Box 9517</deliveryPoint>
				<city>Leiden</city>
				<postalCode>2332 AA</postalCode>
				<country>NL</country>
			</address>
			<phone>+31 71 7519362</phone>
			<electronicMailAddress>support@sp2000.org</electronicMailAddress>
		</contact>		
	</dataset>
	
	<additionalMetadata>
		<metadata>
			<gbif>
				<dateStamp>[dateStamp]</dateStamp>
				<citation identifier="http://www.catalogueoflife.org/col/info/cite">[citation]</citation>
				<resourceLogoUrl>[resourceLogoUrl]</resourceLogoUrl>
			</gbif>
			
			<sourceDatabase>
				<abbreviatedName>[abbreviatedName]</abbreviatedName>
				<authorsAndEditors>[authorsEditors]</authorsAndEditors>
				<version>[version]</version>
			</sourceDatabase>
		</metadata>
	</additionalMetadata>
</eml:eml>

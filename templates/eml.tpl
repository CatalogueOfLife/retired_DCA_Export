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
		
		<creator>
			<organizationName>Species 2000 &amp; ITIS Catalogue of Life</organizationName>
			<address>
				<city>Reading</city>
				<postalCode>RG6 6AS</postalCode>
				<country>UK</country>
			</address>
			<onlineUrl>http://www.catalogueoflife.org</onlineUrl>
		</creator>

		<metadataProvider>
			<organizationName>Species 2000 Secretariat</organizationName>
			<address>
				<city>Reading</city>
				<country>UK</country>
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
			<organizationName>Species 2000 Secretariat</organizationName>
			<address>
				<deliveryPoint>Harborne Building, The University of Reading</deliveryPoint>
				<city>Reading RG6 6AS</city>
				<country>UK</country>
			</address>
			<phone>+44(0)1183786466</phone>
			<electronicMailAddress>support@sp2000.org</electronicMailAddress>
		</contact>		
	</dataset>
	
	<additionalMetadata>
		<metadata>
			<gbif>
				<dateStamp>[dateStamp]</dateStamp>
				<citation identifier="http://www.mapress.com/phytotaxa/content/2011/f/pt00019p054.pdf" >[citation]</citation>
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
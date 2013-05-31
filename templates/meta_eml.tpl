<eml:eml xmlns:eml="eml://ecoinformatics.org/eml-2.1.1"
	xmlns:md="eml://ecoinformatics.org/methods-2.1.1" 
	xmlns:proj="eml://ecoinformatics.org/project-2.1.1"
	xmlns:d="eml://ecoinformatics.org/dataset-2.1.1" 
	xmlns:res="eml://ecoinformatics.org/resource-2.1.1"
	xmlns:dc="http://purl.org/dc/terms/" 
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="eml://ecoinformatics.org/eml-2.1.1 http://rs.gbif.org/schema/eml-gbif-profile/1.0.2/eml.xsd"
	packageId="[pubDate]"
	system="catalogueoflife.org" 
	scope="system" 
	xml:lang="eng">
	<!--
		Most information taken here from the description of the latest online edition:
		http://www.catalogueoflife.org/col/info/ac		
	-->
	<dataset>
		<alternateIdentifier>[issn]</alternateIdentifier>

		<title xml:lang="eng">Catalogue of Life</title>
		
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
		
		<!-- 
			The Catalogue of Life Editors
			For all available, known roles see http://rs.gbif.org/vocabulary/gbif/agent_role.xml
		-->
		<associatedParty>
			<individualName>
				<givenName>Yuri</givenName>
				<surName>Roskov</surName>					
			</individualName>
			<organizationName>CoL Secretariat, University of Reading</organizationName>
			<address>
				<city>Reading</city>
				<country>UK</country>
			</address>			
			<role>editor</role>
		</associatedParty>
		<associatedParty>
			<individualName>
				<givenName>Thomas</givenName>
				<surName>Kunze</surName>					
			</individualName>
			<organizationName>CoL Secretariat, University of Reading</organizationName>
			<address>
				<city>Reading</city>
				<country>UK</country>
			</address>			
			<role>editor</role>
		</associatedParty>
		<!-- 
			The Catalogue of Life Assembly Team			
		-->
		<associatedParty>
			<individualName>
				<givenName>Luvie</givenName>
				<surName>Paglinawan</surName>					
			</individualName>		
			<organizationName>CoL Philippines Office, FIN</organizationName>
			<address>
				<city>Los Baños</city>
				<country>Philippines</country>
			</address>			
			<role>processor</role>			
		</associatedParty>
		<!--
			Annual Checklist Software Development			
		-->
		<associatedParty>
			<individualName>
				<givenName>Wouter</givenName>
				<surName>Addink</surName>					
			</individualName>				
			<organizationName>ETI Bioinformatics</organizationName>
			<address>
				<city>Leiden</city>
				<country>Netherlands</country>
			</address>			
			<role>programmer</role>
		</associatedParty>
		<associatedParty>			
			<individualName>
				<givenName>Dennis</givenName>
				<surName>Seijts</surName>					
			</individualName>
			<organizationName>ETI Bioinformatics</organizationName>
			<address>
				<city>Leiden</city>
				<country>Netherlands</country>
			</address>			
			<role>programmer</role>			
		</associatedParty>
		
		
		<!-- date the GSD data was last updated -->
		<pubDate>[pubDate]</pubDate>
		
		<language>eng</language>
		
		<!-- 
			wrap all text inside the <para> tag
           	multiple paragraphs could be omitted and all content placed into a single <para> element 
		-->
		<abstract>
			<para>This release of the Catalogue of Life contains contributions from [nrDatabases] databases with information on [nrSpecies] species, [nrInfraspecies] infraspecific taxa and also includes [nrSynonyms] synonyms and [nrCommonNames] common names.</para>
			<para>The Catalogue of Life combines the outputs of the Species 2000 and the ITIS programmes. Assembly and publication of the Catalogue of Life is managed by Yuri Roskov in Reading, working with colleagues around the world.</para>
		</abstract>
		
		<!-- multiple paragraphs could be omitted and all content placed into a single <para> element -->
		<intellectualRights>
			<para>© [year], Species 2000</para>
			<para>This online database is copyrighted by Species 2000 on behalf of the Catalogue of Life partners.</para>
			<para>Use of the content (such as the classification, synonymic species checklist, and scientific names) for publications and databases by individuals and organizations for not-for-profit usage is encouraged, on condition that full and precise credit is given at three levels on all occasions that records are shown. The three-part credit includes the complete work, the contributing database of the record, and the expert who provides taxonomic scrutiny of the individual record. For example, these might be as follows:
				
				1) Species 2000 &amp; ITIS Catalogue of Life: 2013 Annual Checklist,
				2) The full or short name of the contributing database, such as: Coreoidea Species File or CoreoideaSF,
				3) When provided, the latest taxonomic scrutiny field (specialist name &amp; date), such as Lohrmann V. &amp; Kroupa A.S., 05-Oct-2012 (from HymIS Rhopalosomatidae) or Barber-James H., Sartori M., Gattolliat J.L. &amp; Webb J., Feb-2013 (from FADA Ephemeroptera).
				This three-part credit can in many cases be kept brief by using a standard form and one or more logos as in the following example: "Species recognized by J. van Tol, Odonata database, ver. Dec 2011 in ". You may copy the Catalogue of Life logo from this example, on the DVD and web versions, for use in giving credit.
				
				If you wish to use the content on a public portal or webpage you are required to notify the Species 2000 Secretariat, both to request written permission and to assist with a check that the correct credits are given.
			</para>
			<para>Commercial Use				
				Use of this compilation or any of the databases contained within it by commercial organisations requires a written agreement from Species 2000 and ITIS. This applied when it is used commercially for external services, products or publications and when it is used to provide internal services within an organisation.
			</para>
			<!-- not sure if a disclaimer is IPR, but wont hurt to include it -->
			<para>Disclaimer				
				The Species 2000 &amp; ITIS Catalogue of Life editorial board cannot guarantee the accuracy or completeness of the information in this edition. Be aware that the Catalogue of Life is still incomplete and undoubtedly contains errors. Neither Species 2000 &amp; ITIS nor any contributing database can be made liable for any direct or indirect damage arising out of the use of Catalogue of Life.
			</para>
		</intellectualRights>
		
		<!-- link to a GSD website -->
		<distribution scope="document">
			<online>
				<url function="information">[website]</url>
			</online>
		</distribution>
		
		<coverage>
			<geographicCoverage>
				<geographicDescription>Global</geographicDescription>
			</geographicCoverage>
			
			<taxonomicCoverage>
				<generalTaxonomicCoverage>This release of the Catalogue of Life contains contributions from [nrDatabases] databases with information on [nrSpecies] species, [nrInfraspecies] infraspecific taxa and also includes [nrSynonyms] synonyms and [nrCommonNames] common names.</generalTaxonomicCoverage>
				<!-- 
					the following is fixed and simply lists all kingdoms covered. 
					Listing of all included families or other ranks could also be done, but might be a bit too much (some EML publisher do this though) 
				-->
				<taxonomicClassification>
					<taxonRankName>Kingdom</taxonRankName>
					<taxonRankValue>Viruses</taxonRankValue>
				</taxonomicClassification>
				<taxonomicClassification>
					<taxonRankName>Kingdom</taxonRankName>
					<taxonRankValue>Bacteria</taxonRankValue>
				</taxonomicClassification>
				<taxonomicClassification>
					<taxonRankName>Kingdom</taxonRankName>
					<taxonRankValue>Archaea</taxonRankValue>
				</taxonomicClassification>
				<taxonomicClassification>
					<taxonRankName>Kingdom</taxonRankName>
					<taxonRankValue>Chromista</taxonRankValue>
				</taxonomicClassification>
				<taxonomicClassification>
					<taxonRankName>Kingdom</taxonRankName>
					<taxonRankValue>Protozoa</taxonRankValue>
				</taxonomicClassification>
				<taxonomicClassification>
					<taxonRankName>Kingdom</taxonRankName>
					<taxonRankValue>Fungi</taxonRankValue>
				</taxonomicClassification>
				<taxonomicClassification>
					<taxonRankName>Kingdom</taxonRankName>
					<taxonRankValue>Plantae</taxonRankValue>
				</taxonomicClassification>
				<taxonomicClassification>
					<taxonRankName>Kingdom</taxonRankName>
					<taxonRankValue>Animalia</taxonRankValue>
				</taxonomicClassification>
			</taxonomicCoverage>			
		</coverage>
		
		<!-- whom to contact in case of questions or improvements to the actual data -->
		<contact>
			<organizationName>Species 2000 Secretariat</organizationName>
			<address>
				<deliveryPoint>Harborne Building, The University of Reading</deliveryPoint>
				<city>Reading RG6 6AS</city>
				<country>UK</country>
			</address>
			<phone>+44(0)1183786466</phone>
			<!-- vital, cause this will be used for feedback -->
			<electronicMailAddress>support@sp2000.org</electronicMailAddress>
		</contact>
		
	</dataset>
	
	<additionalMetadata>
		<metadata>
			<gbif>
				<!-- date this EML file was created -->
				<dateStamp>[dateStamp]</dateStamp>
				<!-- how to cite the entire catalogue of life -->
				<citation identifier="[issn]">[citation]</citation>
				<resourceLogoUrl>[resourceLogoUrl]</resourceLogoUrl>				
			</gbif>
		</metadata>
	</additionalMetadata>
</eml:eml>
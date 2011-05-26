<archive xmlns='http://rs.tdwg.org/dwc/text/'
   xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'
   xsi:schemaLocation='http://rs.tdwg.org/dwc/text/ http://rs.tdwg.org/dwc/text/tdwg_dwc_text.xsd'>
   
  <core encoding='UTF-8' fieldsEnclosedBy='[sep]' fieldsTerminatedBy='[del]' linesTerminatedBy='\n' ignoreHeaderLines='1' rowType='http://rs.tdwg.org/dwc/terms/Taxon'>    
    <files>
      <location>taxa.txt</location>
    </files>
    <id index='0' />
    <field index="0" term="http://rs.tdwg.org/dwc/terms/taxonID"/>
    <field index="1" term="http://purl.org/dc/terms/identifier"/>
    <field index="2" term="http://rs.tdwg.org/dwc/terms/datasetID"/>
		<field index="3" term="http://rs.tdwg.org/dwc/terms/datasetName"/>
    <field index='4' term='http://rs.tdwg.org/dwc/terms/acceptedNameUsageID'/>
    <field index='5' term='http://rs.tdwg.org/dwc/terms/parentNameUsageID'/>
    <field index='6' term='http://rs.tdwg.org/dwc/terms/taxonomicStatus'/> 
    <field index='7' term='http://rs.tdwg.org/dwc/terms/taxonRank'/>
    <field index='8' term='http://rs.tdwg.org/dwc/terms/scientificName'/>
    <field index='9' term='http://rs.tdwg.org/dwc/terms/kingdom'/>
    <field index='10' term='http://rs.tdwg.org/dwc/terms/phylum'/>
    <field index='11' term='http://rs.tdwg.org/dwc/terms/class'/>
    <field index='12' term='http://rs.tdwg.org/dwc/terms/order'/>
    <field index='13' term='http://rs.tdwg.org/dwc/terms/family'/>
    <field index='14' term='http://rs.tdwg.org/dwc/terms/genus'/>
    <field index='16' term='http://rs.tdwg.org/dwc/terms/subgenus'/>
    <field index='17' term='http://rs.tdwg.org/dwc/terms/specificEpithet'/>
    <field index='18' term='http://rs.tdwg.org/dwc/terms/infraspecificEpithet'/>
    <field index='19' term='http://rs.tdwg.org/dwc/terms/scientificNameAuthorship'/>
    <field index='20' term='http://rs.tdwg.org/dwc/terms/nameAccordingTo'/>    
    <field index='21' term='http://purl.org/dc/terms/modified'/>
  </core>
  
  <extension encoding='UTF-8' fieldsEnclosedBy='[sep]' fieldsTerminatedBy='[del]' linesTerminatedBy='\n' ignoreHeaderLines='1' rowType='http://rs.gbif.org/terms/1.0/Identifier'>
		<files>
			<location>taxa.txt</location>
		</files>
		<field index="1" term="http://purl.org/dc/terms/identifier"/>
    <field default="LSID" term="http://purl.org/dc/terms/type"/>
	</extension>
	
  <extension encoding='UTF-8' fieldsEnclosedBy='[sep]' fieldsTerminatedBy='[del]' linesTerminatedBy='\n' ignoreHeaderLines='1' rowType='http://rs.gbif.org/terms/1.0/Distribution'>
    <files>
      <location>distribution.txt</location>
    </files>
    <coreid index='0' />
    <field index='1' term='http://rs.tdwg.org/dwc/terms/occurrenceStatus'/>
    <field index='2' term='http://rs.tdwg.org/dwc/terms/locationID'/>
    <field index='3' term='http://rs.tdwg.org/dwc/terms/locality'/>             
    <field index='4' term='http://rs.tdwg.org/dwc/terms/establishmentMeans'/>                                            
  </extension>
  
  <extension encoding='UTF-8' fieldsEnclosedBy='[sep]' fieldsTerminatedBy='[del]' linesTerminatedBy='\n' ignoreHeaderLines='1' rowType='http://rs.gbif.org/terms/1.0/Description'>
    <files>
      <location>description.txt</location>
    </files>
    <coreid index='0' />
    <field index='1' term='http://purl.org/dc/terms/type'/>
    <field index='2' term='http://purl.org/dc/terms/description'/>
  </extension>
  
  <extension encoding='UTF-8' fieldsEnclosedBy='[sep]' fieldsTerminatedBy='[del]' linesTerminatedBy='\n' ignoreHeaderLines='1' rowType='http://rs.gbif.org/terms/1.0/Reference'>
    <files>
      <location>references.txt</location>
    </files>
    <coreid index='0' />
    <field index='1' term='http://purl.org/dc/terms/title'/>
    <field index='2' term='http://purl.org/dc/terms/creator'/>
    <field index='3' term='http://purl.org/dc/terms/date'/>
    <field index='4' term='http://purl.org/dc/terms/description'/>
    <field index='5' term='http://purl.org/dc/terms/identifier'/>
  </extension>
  
   <extension encoding='UTF-8' fieldsEnclosedBy='[sep]' fieldsTerminatedBy='[del]' linesTerminatedBy='\n' ignoreHeaderLines='1' rowType='http://rs.gbif.org/terms/1.0/SpeciesProfile'>
     <files>
       <location>speciesprofile.txt</location>
     </files>
     <coreid index='0' />
     <field index='1' term='http://rs.tdwg.org/dwc/terms/habitat'/>
   </extension>  
   
   <extension encoding='UTF-8' fieldsEnclosedBy='[sep]' fieldsTerminatedBy='[del]' linesTerminatedBy='\n' ignoreHeaderLines='1' rowType='http://rs.gbif.org/terms/1.0/VernacularName'>
     <files>
       <location>vernacular.txt</location>
     </files>
     <coreid index='0' />
     <field index='1' term='http://rs.tdwg.org/dwc/terms/vernacularName'/>
     <field index='2' term='http://purl.org/dc/terms/language'/>
     <field index='3' term='http://rs.tdwg.org/dwc/terms/countryCode'/>
     <field index='4' term='http://rs.tdwg.org/dwc/terms/locality'/>
     <field index='5' term='http://purl.org/dc/terms/source'/>
   </extension>         
                             
</archive>
<?php

/**
 * Service for providing information about services in the catalog
 */

namespace app\Services;

class Service {

  /**
   * Get all servcies
   *
   * @return Array all the services
   */
  public static function services() {
    
      $services = array();
      
      $web_services = array();
      
      $web_services[] = array(
          'title' => 'Visual Media Service',
          'description' => 'The ARIADNE Visual Media Service provides easy publication and presentation on the web of complex visual media assets. It is an automatic service that allows to upload visual media files on an ARIADNE server and to transform them into an efficient web format, making them ready for web-based visualization.',
          'url' => 'http://visual.ariadne-infrastructure.eu/',
          'image' => 'visual_media_service.png'
      );
      
      $web_services[] = array(
          'title' => 'Landscape Factory',
          'description' => 'Landscape Services for ARIADNE are a set of responsive web services that include large terrain dataset generation, 3D landscape composing and 3D model processing, leveraging powerful open-source frameworks and toolkits such as GDAL, OSGjs, OpenSceneGraph and ownCloud. The main components include: the cloud service, the terrain generation service, the terrain gallery and the front-end web component for interactive visualization.',
          'url' => 'http://landscape.ariadne-infrastructure.eu/',
          'image' => 'landscape_services.png'
      );
      
      $web_services[] = array(
          'title' => 'DCCD: Digital Collaboratory for Cultural Dendrochronology',
          'description' => "To improve European integration of dendrochronological data, DANS has now made it possible for others to use the same software as the DCCD-repository of DANS, and use existing components to create their own dendrochronological archive that is also ARIADNE compatible. This open source software is available from the following GitHub repository: https://github.com/DANS-KNAW/dccd-webui
The DCCD software is an online digital archiving system for dendrochronological data. A recent version of this software (system) is deployed as 'Digital Collaboratory for Cultural Dendrochronology' (DCCD) at http://dendro.dans.knaw.nl. 
More information about the Digital Collaboratory for Cultural Dendrochronology (DCCD) project can be found here: http://vkc.library.uu.nl/vkc/dendrochronology.
The DCCD is the primary archaeological/historical tree-ring (meta)data network existing in Europe. It became operational in 2011. Within the DCCD Belgian, Danish, Dutch, German, Latvian, Polish, and Spanish laboratories have joined data in a manner that suits their shared and individual research agendas. In its present state the DCCD contains measurement series of different wood species derived from objects and sites dating between 6000 BC and present. All data sets are described with very detailed metadata according to the newly developed international dendrochronological data standard TRiDaS (Jansma et al. 2010). The collection is derived by research from archaeological sites (including old landscapes), shipwrecks, historical architecture and mobile heritage (e.g. paintings, furniture).",
          'url' => 'http://dendro.dans.knaw.nl/',
          'image' => 'dendro_service.png'
      );      
            
      $web_services[] = array(
          'title' => 'IDAI.vocab',
          'description' => 'The new DAI Thesaurus of Archaeological Concepts was designed from the onset as a thesaurus of German words and phrases with significant multilingual support. The core of the thesaurus is a list of concepts related to the domain of archaeology (nouns, verbs, less frequently adjectives, but also complex phrases that point to a specific object, such as “carrarischer Marmor”) all linked to corresponding translations in a wide spectrum of different languages; we also  established a minimal set of relations between the German terms (synonyms, direct hyper- and hyponyms), and grouped the equivalent terms together; whenever it is possible, we also resolved equivalent terms by selecting one preferred concept. In addition we connect terms and concepts by SKOS links to external thesauri, like the Arts & Architecture Thesaurus of the Getty Institution.',
          'url' => 'http://archwort.dainst.org/thesaurus/de/vocab/index.php',
          'image' => 'idai_vocab_service.png'
      );
      
      $web_services[] = array(
          'title' => 'IDAI.gazetteer',
          'description' => 'The German Archaeological Institute together with the Cologne Digital Archaeology Laboratory is developing the iDAI.gazetteer - a web service connecting toponyms with coordinates. It was initially built as an authority file/controlled vocabulary for any geo-related information in information systems of the DAI. Furthermore it is meant to link these data with other worldwide gazetteer-systems.',
          'url' => 'http://gazetteer.dainst.org/',
          'image' => 'idai_gazetteer_service.png'
      );    
      
      $web_services[] = array(
          'title' => 'Heritage Data: Linked Data Vocabularies for Cultural Heritage',
          'description' => 'National cultural heritage thesauri and vocabularies have acted as standards for use by both national organizations and local authority Historic Environment Records but until now have lacked the persistent Linked Open Data (LOD) URIs that would allow them to act as vocabulary hubs for the Web of Data. The AHRC funded SENESCHAL project aims to make such vocabularies available online as Semantic Web resources. SENESCHAL will start with major vocabularies as exemplars and project partners will continue to make other vocabularies available. Other organizations are welcome to make use of the data and services which will be open licensed.',
          'url' => 'http://www.heritagedata.org/blog/services/',
          'image' => 'heritage_data_service.png'
      );    
      
      $web_services[] = array(
          'title' => 'Heritage Data: Vocabulary matching tool',
          'description' => 'The Vocabulary Matching Tool was developed for aligning Linked Data vocabulary terms with Getty Art & Architecture Thesaurus concepts. The source code for the Vocabulary Matching Tool is also available for local download and installation (https://github.com/cbinding/VocabularyMatchingTool) - in which case it might then be classed as “stand alone services” (tools to be downloaded and installed on one’s machine).',
          'url' => 'http://heritagedata.org/vocabularyMatchingTool/',
          'image' => 'vocabulary_matching_tool_service.png'
      );             
      
      $services['web_services'] = $web_services; 
      
      $stand_alone_services = array();
      
      $stand_alone_services[] = array(
          'title' => 'MeshLab',
          'description' => 'MeshLab is an open source, portable, and extensible system for the processing and editing of unstructured 3D triangular meshes.
The system is aimed to help the processing of the typical not-so-small unstructured models arising in 3D scanning, providing a set of tools for editing, cleaning, healing, inspecting, rendering and converting this kind of meshes. In the archeoleogical field, MeshLab is strongly used by the community in the context of 3D reconstruction from scanning, mesh cleaning, mesh comparison and data presentation.',
          'url' => 'http://meshlab.sourceforge.net/',
          'image' => 'meshlab_service.png'
      );    
            
      $services['stand_alone_services'] = $stand_alone_services; 
      
      $institutional_services = array();
      
      $institutional_services[] = array(
          'title' => 'Archeology Data Service',
          'description' => 'The Archaeology Data Service is the national digital data archive for archaeology the UK and a world-leading data management centre for the archaeology and heritage sector.  ADS-Easy provides an online costing tool and data deposit service. The ADS operates according to the OAIS model for digital archives and holds the Data Seal of Approval, the internationally recognized quality mark for trusted digital repositories. In 2012 the ADS was awarded the Digital Preservation Coalition’s Decennial Award for the most outstanding contribution to digital preservation of the last decade.',
          'url' => 'http://archaeologydataservice.ac.uk',
          'image' => 'ads_service.png'
      );    
      
      $institutional_services[] = array(
          'title' => 'DANS: Data Archiving and Networked Services',
          'description' => "The e-depot for Dutch archaeology is accommodated at DANS, the national digital research data archive for the Netherlands. A wealth of digital archaeological excavation data such as maps, field drawings, photographs, tables and publications is accessible via EASY, DANS’ online archiving (deposit, preservation and reuse) service. DANS operates according to the OAIS model for digital archives and holds the Data Seal of Approval, the internationally recognized quality mark for trusted digital repositories.
DANS was established in 2005, with predecessors dating back to 1964, and now comprises some 45 staff. DANS's activities are centred around 3 core services: data archiving, data reuse, training and consultancy. Driven by data, DANS ensures the further improvement of sustained access to digital research data with its services and participation in (inter)national projects and networks. DANS is an institute of the Royal Netherlands Academy of Arts and Sciences (KNAW) and co-founded by the Netherlands Organization for Scientific Research (NW0).",
          'url' => 'http://dans.knaw.nl',
          'image' => 'dans_service.png'
      );    

      $institutional_services[] = array(
          'title' => 'ARACHNE',
          'description' => 'Arachne is the central Object database of the German Archaeological Institute (DAI) and the Archaeological Institute of the University of Cologne. Arachne is intended to provide archaeologists and Classicists with a free internet research tool for quickly searching hundreds of thousands of records on objects and their attributes. It provides an online data deposit service.This combines an ongoing process of digitizing traditional documentation (stored on media which are both threatened by decay and largely unexplored) with the production of new digital object and graphic data. Wherever possible, Arachne follows a paradigm of highly structurized object-metadata which is mapped onto the CIDOC-CRM, to address machine-readable metadata strategies of the Semantic Web. This “structured world” of Arachne requires large efforts in time and money and is therefore only possible for privileged areas of data. While there is an ever-increasing range of new, “born digital” data, in reality only a small effort-per-object ratio can be applied. It therefore requires a “low-threshold” processing structure which is located in the “unstructured world” of Arachne. All digital (graphic and textual) information is secure on a Tivoli Storage System (featuring long-term multiple redundandancy) and distributed online through the Storage Area Network in Cologne via AFS.',
          'url' => 'http://arachne.dainst.org',
          'image' => 'arachne_service.png'
      );          
            
      $services['institutional_services'] = $institutional_services;       
      
      $human_services = array();
      
      $human_services[] = array(
          'title' => 'Thesaurus RA - Strumenti terminologici Scheda RA Reperti Archeologici',
          'description' => 'The RA terminological tool, curated by ICCU and VAST-LAB, constitutes a reworked version of the RA Thesaurus issued by the ICCD. The RA Thesaurus provides all the necessary terminological facilities for an efficent and well structured recording of the object coming from archaeological excavations.The vocabulary has been implemented by ICCD to support the encoding of two specific fields (OGTD - CLS). These two fields describe the definition of the object and its class and production.',
          'url' => 'http://vast-lab.org/thesaurus/ra/vocab/index.php',
          'image' => 'thesaurus_ra_service.png'
      );         
      
      $services['human_services'] = $human_services;     
      
      return $services;
  }
}
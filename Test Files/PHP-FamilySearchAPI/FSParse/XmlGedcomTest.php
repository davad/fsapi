<?php

require_once dirname(__FILE__) . '/../../../PHP-FamilySearchAPI/FSParse/XMLGEDCOM.php';
require_once dirname(__FILE__) . '/../../../PHP-FamilySearchAPI/FSAPI/FamilySearchProxy.php';

/**
 * Test class for XmlGedcom.
 * Generated by PHPUnit on 2010-11-24 at 14:50:10.
 */
class XmlGedcomTest extends PHPUnit_Framework_TestCase {

    /**
     * @var XmlGedcom
     */
    protected $xmlGed;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->xmlGed = new XmlGedcom;

        $url = 'http://www.dev.usys.org';
        $username = 'api-user-3005';
        $password = 'f6d4';
        $key = 'WCQY-7J1Q-GKVV-7DNM-SQ5M-9Q5H-JX3H-CMJK';

        //--create a new object of FamilySearchProxy
        $proxy = new FamilySearchProxy(
                $url,
                $username,
                $password,
                $key);

        $this->xmlGed->setProxy($proxy);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {

    }

    /**
     * @todo Implement testGetPersons().
     */
    public function testGetPersons() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetMatches().
     */
    public function testGetMatches() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetPerson().
     */
    public function testGetPerson() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testSetProxy().
     */
    public function testSetProxy() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     */
    public function testGetProxy() {
        $proxy = $this->xmlGed->getProxy();
        $this->assertNotNull($proxy);
    }

    /**
     * @todo Implement testClearPersons().
     */
    public function testClearPersons() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testParseXml().
     */
    public function testParseXml() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testToXml().
     */
    public function testToXml() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetIndiGedcom().
     */
    public function testGetIndiGedcom() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetGedcomRecord().
     */
    public function testGetGedcomRecord() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     */
    public function testBuildSearchQuery() {
        $person = new XG_Person();
        $person->name = 'John Smith';
        $query = $this->xmlGed->buildSearchQuery($person);
        $this->assertEquals('name=John+Smith', $query);
    }

    /**
     */
    public function testQuerySubmission() {
        $person = new XG_Person();
        $person->name = 'John Smith';
        $query = $this->xmlGed->buildSearchQuery($person);
        $this->assertEquals('name=John+Smith', $query);
        $response = $this->xmlGed->getProxy()->getPerson($query.'&maxResults=3', false);
        $this->xmlGed->parseXml($response);

        $matches = $this->xmlGed->getMatches();
//        print "matches: ". count($matches) ."\n";
//        foreach($matches as &$match)
//        {
//            print_r($match->getPerson()->getPrimaryName()->getFullText());
//        }
//        print_r($matches);
        $this->assertEquals('John Doe', $matches['KW3B-JVJ']->getPerson()->getPrimaryName()->getFullText());
    }

    /**
     * @todo Implement testConvertGedcomEvent().
     */
    public function testConvertGedcomEvent() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testAddPGVPerson().
     */
    public function testAddPGVPerson() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetRelationshipXml().
     */
    public function testGetRelationshipXml() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetFamCGedcom().
     */
    public function testGetFamCGedcom() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetFamSGedcom().
     */
    public function testGetFamSGedcom() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testAssertionsToXml().
     */
    public function testAssertionsToXml() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testStartElement().
     */
    public function testStartElement() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testEndElement().
     */
    public function testEndElement() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testData().
     */
    public function testData() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOpenError().
     */
    public function testOpenError() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOpenNote().
     */
    public function testOpenNote() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOpenCitation().
     */
    public function testOpenCitation() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOpenPerson().
     */
    public function testOpenPerson() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOpenFamily().
     */
    public function testOpenFamily() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOpenMatch().
     */
    public function testOpenMatch() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOpenId().
     */
    public function testOpenId() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOpenChild().
     */
    public function testOpenChild() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOpenParent().
     */
    public function testOpenParent() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOpenSpouse().
     */
    public function testOpenSpouse() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOpenOrdinance().
     */
    public function testOpenOrdinance() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOpenFact().
     */
    public function testOpenFact() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOpenEvent().
     */
    public function testOpenEvent() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOpenEventValue().
     */
    public function testOpenEventValue() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOpenCharacteristic().
     */
    public function testOpenCharacteristic() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOpenRelationship().
     */
    public function testOpenRelationship() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOpenName().
     */
    public function testOpenName() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOpenGender().
     */
    public function testOpenGender() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOpenPlace().
     */
    public function testOpenPlace() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOpenDate().
     */
    public function testOpenDate() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOpenSelected().
     */
    public function testOpenSelected() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOpenForm().
     */
    public function testOpenForm() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOpenPieces().
     */
    public function testOpenPieces() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testOpenContributor().
     */
    public function testOpenContributor() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testDataError().
     */
    public function testDataError() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testDataCode().
     */
    public function testDataCode() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testDataId().
     */
    public function testDataId() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testDataLiving().
     */
    public function testDataLiving() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testDataModified().
     */
    public function testDataModified() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testDataModifiable().
     */
    public function testDataModifiable() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testDataScore().
     */
    public function testDataScore() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testDataConfidence().
     */
    public function testDataConfidence() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testDataNote().
     */
    public function testDataNote() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testDataCitation().
     */
    public function testDataCitation() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testDataOriginal().
     */
    public function testDataOriginal() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testDataSelected().
     */
    public function testDataSelected() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testDataFullText().
     */
    public function testDataFullText() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testDataNormalized().
     */
    public function testDataNormalized() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testDataDetail().
     */
    public function testDataDetail() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testDataAge().
     */
    public function testDataAge() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testDataTitle().
     */
    public function testDataTitle() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testDataDescription().
     */
    public function testDataDescription() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testDataDisposition().
     */
    public function testDataDisposition() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testDataTemple().
     */
    public function testDataTemple() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testDataMinYear().
     */
    public function testDataMinYear() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testDataMaxYear().
     */
    public function testDataMaxYear() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testDataValue().
     */
    public function testDataValue() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testDataType().
     */
    public function testDataType() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testValueGender().
     */
    public function testValueGender() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testValuePieces().
     */
    public function testValuePieces() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testPiecePrefix().
     */
    public function testPiecePrefix() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testPieceSuffix().
     */
    public function testPieceSuffix() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testPieceGiven().
     */
    public function testPieceGiven() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testPieceFamily().
     */
    public function testPieceFamily() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testPieceOther().
     */
    public function testPieceOther() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}
?>

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ApiTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->load_data();
    }

    public function test_get_endpoint(): void
    {
        // Test content contains 90 funds
        $response = $this->get('/api/funds');
        $response->assertStatus(200);
        $content = json_decode($response->getContent());
        $this->assertEquals(90, count($content));
    }

    public function test_get_endpoint_filters(): void
    {
        // There are 3 funds from 2009
        $response = $this->get('/api/funds?year=2009');
        $content  = json_decode($response->getContent());
        $this->assertEquals(3, count($content));

        // There are 28 funds from > 2009
        $response = $this->get('/api/funds?year=>2009');
        $content  = json_decode($response->getContent());
        $this->assertEquals(28, count($content));

        // Fund Manager 10 manages 2 funds from > 2009
        $response = $this->get('/api/funds?year=>2009&fund_manager_id=10');
        $content  = json_decode($response->getContent());
        $this->assertEquals(2, count($content));

        // Fund Manager 10 manages 1 fund named LSL-CAF from > 2009
        $response = $this->get('/api/funds?year=>2009&fund_manager_id=10&name=LSL-CAF');
        $content  = json_decode($response->getContent());
        $this->assertEquals(1, count($content));
    }

    public function test_get_endpoint_ordering(): void
    {
        // If no ordering is specified, the default is by id ASC
        $response = $this->get('/api/funds');
        $content  = json_decode($response->getContent());
        $this->assertEquals(1, $content[0]->id);

        // Ordering by ID DESC
        $response = $this->get('/api/funds?order=DESC');
        $content  = json_decode($response->getContent());
        $this->assertEquals(90, $content[0]->id);

        // Ordering by name DESC
        $response = $this->get('/api/funds?order=DESC&order_by=name');
        $content  = json_decode($response->getContent());
        $this->assertEquals('ZAR-YEM', $content[0]->name);

        // Ordering by fund manager
        $response = $this->get('/api/funds?order_by=fund_manager_id');
        $content  = json_decode($response->getContent());
        $this->assertEquals('JPY-AUT', $content[0]->name);

        // Ordering by fund manager then by name
        $response = $this->get('/api/funds?order_by=fund_manager_id,name');
        $content  = json_decode($response->getContent());
        $this->assertEquals('BIF-LBR', $content[0]->name);
    }

    public function test_create_update_fund(): void
    {
        // Fund manager 40 has no funds
        $response = $this->get('/api/funds?fund_manager_id=40');
        $content  = json_decode($response->getContent());
        $this->assertEquals(0, count($content));

        // Create a new fund for FM 40
        $response = $this->post('/api/funds', [
            'name'            => 'Test Fund',
            'fund_manager_id' => 40,
            'year'            => 2020,
            'companies'       => ['add' => [1, 2, 3]],
        ]);

        $content = json_decode($response->getContent());
        $this->assertEquals('Test Fund', $content->name);
        $this->assertEquals('Turcotte LLC', $content->fund_manager);
        $this->assertEquals(40, $content->fund_manager_id);
        $this->assertEquals(2020, $content->year);
        $this->assertEquals(3, count($content->companies));

        // Update the newly created fund, changing all fields, adding 3 companies and removing 2
        $response = $this->put('/api/funds/91', [
            'name'            => 'Modified Fund',
            'fund_manager_id' => 20,
            'year'            => 1980,
            'companies'       => ['add' => [10, 20, 30], 'remove' => [1, 2]],
        ]);

        $content = json_decode($response->getContent());
        $this->assertEquals('Modified Fund', $content->name);
        $this->assertEquals('Langosh, Labadie and Herzog', $content->fund_manager);
        $this->assertEquals(20, $content->fund_manager_id);
        $this->assertEquals(1980, $content->year);
        $this->assertEquals(4, count($content->companies));
    }

    public function test_create_duplicated_funds(): void
    {
        // Create a new fund with the same name and manager as another
        $response = $this->post('/api/funds', [
            'name'            => 'MZN-IRN',
            'fund_manager_id' => 5,
            'year'            => 2020,
            'companies'       => ['add' => [11, 22, 33]],
        ]);

        // Fund is properly created
        $content = json_decode($response->getContent());
        $this->assertEquals('MZN-IRN', $content->name);
        $this->assertEquals('Gusikowski, Hickle and Greenfelder', $content->fund_manager);
        $this->assertEquals(5, $content->fund_manager_id);
        $this->assertEquals(2020, $content->year);
        $this->assertEquals(3, count($content->companies));

        // Two funds with the same name exist
        $response = $this->get('/api/funds?name=MZN-IRN');
        $content  = json_decode($response->getContent());
        $this->assertEquals(2, count($content));

        // Create a new fund with the same name and manager as another fund's alias
        $response = $this->post('/api/funds', [
            'name'            => 'DeepSkyBlue',
            'fund_manager_id' => 5,
            'year'            => 2020,
            'companies'       => ['add' => [44, 23, 32]],
        ]);

        // Fund is properly created
        $content = json_decode($response->getContent());
        $this->assertEquals('DeepSkyBlue', $content->name);
        $this->assertEquals('Gusikowski, Hickle and Greenfelder', $content->fund_manager);
        $this->assertEquals(5, $content->fund_manager_id);
        $this->assertEquals(2020, $content->year);
        $this->assertEquals(3, count($content->companies));

        // Two logs for possible duplicates were generated with the proper info for the possible duplicate properties
        $logs = DB::table('fund_duplicates_log')->get();
        $this->assertEquals(2, count($logs));
        $this->assertEquals( 91, $logs[0]->fund_id );
        $this->assertTrue( json_validate( $logs[0]->duplicates) );
        $this->assertEquals( 92, $logs[1]->fund_id );
        $this->assertTrue( json_validate( $logs[1]->duplicates) );
    }

    public function load_data(): void
    {
        DB::unprepared(file_get_contents(dirname(__DIR__) . '/_support/data/test_data.sql'));
    }

}

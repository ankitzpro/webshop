<?php

namespace App\Console\Commands;

use App\Models\Customers;
use App\Models\Products;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ImportCSVData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import_csv_data';

    protected $auth = [
        'auth' => ["loop", "backend_dev"],
    ];


    protected $products_csv_url = "https://backend-developer.view.agentur-loop.com/products.csv";
    protected $customers_csv_url = "https://backend-developer.view.agentur-loop.com/customers.csv";

    protected $products_header =['id','product_name','price'];
    protected $customers_header =['id','job_title','email','full_name','registered_date','phone'];

    

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to import Products and Customers data from CSV';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
    //Importing Products
    $this->importData('products');

    //Importing Customers
    $this->importData('customers');
    }

    public function importData($type){
        
        $urltype= $type.'_csv_url';
        $headertype = $type.'_header';
        Log::info("Import ".$type." Started");
        try{
        // Fetch the CSV data from the URL
        $client = new Client();
        $response = $client->get($this->$urltype,$this->auth);
        $csvData = $response->getBody()->getContents();

        $count=0;

        // Convert the CSV data into an array
        $rows = array_map('str_getcsv', explode("\n", $csvData));
        array_shift($rows);
        $header = $this->$headertype;
        $chunks = array_chunk($rows, 500);
        foreach ($chunks as $chunk) {
            $data = [];
            foreach ($chunk as $row) {
                // Convert the date format for registered date
                if($type == 'customers'){
                    $row[4] = $this->getDateString($row[4]);
                }
                $data[] = array_merge(array_combine($header, $row),[
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $count++;
            }

            // Entry into Database
            if($type == 'customers'){
            Customers::insert($data);
            }
            else{
            Products::insert($data);
            }
            }
            Log::info("Import ".$type." Complete, Imported data:".$count);
        }
        catch(Exception $e){
            Log::error("Import ".$type." Error ".$e->getMessage());
        }

    }

    public function getDateString($string){

        $new_string = substr($string, strpos($string, ",") + 1);

        return Carbon::parse($new_string)->toDateString();

    }

}

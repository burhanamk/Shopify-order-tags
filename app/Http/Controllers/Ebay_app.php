<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use \DTS\eBaySDK\Constants;
use \DTS\eBaySDK\Trading\Types;
use \DTS\eBaySDK\Trading\Enums;
use \Hkonnet\LaravelEbay\EbayServices;
use \Hkonnet\LaravelEbay\Ebay;

class Ebay_app extends Controller {

    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */
    public function showProfile()
    {
       
        
/**
 * Create the service object.
 */
$ebay_service = new EbayServices();
$service = $ebay_service->createTrading();

/**
 * Create the request object.
 */
$request = new Types\GetStoreRequestType();
/**
 * An user token is required when using the Trading service.
 */
$ebay= new Ebay;

$request->RequesterCredentials = new Types\CustomSecurityHeaderType();
$authToken = $ebay->getAuthToken();
//print_r($authToken);
//exit;
$request->RequesterCredentials->eBayAuthToken = $authToken;
$response = $service->getStore($request);
    dd($response);
/**
 * Request that eBay returns the list of actively selling items.
 * We want 10 items per page and they should be sorted in descending order by the current price.
 */
$request->ActiveList = new Types\ItemListCustomizationType();
$request->ActiveList->Include = true;
$request->ActiveList->Pagination = new Types\PaginationType();
$request->ActiveList->Pagination->EntriesPerPage = 10;
$request->ActiveList->Sort = Enums\ItemSortTypeCodeType::C_CURRENT_PRICE_DESCENDING;
$pageNum = 1;

do {
    $request->ActiveList->Pagination->PageNumber = $pageNum;
    
    /**
     * Send the request.
     */
    $response = $service->getMyeBaySelling($request);

    /**
     * Output the result of calling the service operation.
     */
    echo "==================\nResults for page $pageNum\n==================\n";
    if (isset($response->Errors)) {
        foreach ($response->Errors as $error) {
            printf(
                "%s: %s\n%s\n\n",
                $error->SeverityCode === Enums\SeverityCodeType::C_ERROR ? 'Error' : 'Warning',
                $error->ShortMessage,
                $error->LongMessage
            );
        }
    }
    if ($response->Ack !== 'Failure' && isset($response->ActiveList)) {
        foreach ($response->ActiveList->ItemArray->Item as $item) {
            printf(
                "(%s) %s: %s %.2f\n",
                $item->ItemID,
                $item->Title,
                $item->SellingStatus->CurrentPrice->currencyID,
                $item->SellingStatus->CurrentPrice->value
            );
        }
    }
    $pageNum += 1;
} while (isset($response->ActiveList) && $pageNum <= $response->ActiveList->PaginationResult->TotalNumberOfPages);
    }

}
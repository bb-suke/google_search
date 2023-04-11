<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class GoogleSearchController extends BaseController
{
    function index(Request $request)
    {
        require_once '../vendor/autoload.php';

        $apiKey = config('constants.google_search_json_api.api_key');
        $cx = config('constants.google_search_json_api.search_engine_id');

        $googleSearchUrl = "https://www.googleapis.com/customsearch/v1?";

        $searchRequest = $request['search_request'];
        $page = $request['page'];

        //検索一覧から取得する最初の値
        $startNum = 1;

        //一度に取得したいページ数
        $totalPageNum = 2;

        //１ページに表示する検索結果の数
        $displayItemNum = 5;

        $searchInfos = [];
        $displayItems = [];

        if (!empty($searchRequest)) {

        for ($i=0; $i <= $totalPageNum ; $i++) {
            //MEMO リクエスト出来るクエリの項目はこちらで確認できます-> https://developers.google.com/custom-search/v1/reference/rest/v1/cse/list?hl=ja
            $requestParams = array(
                'key' => $apiKey,
                'cx' => $cx,
                'q' => $searchRequest,
                'alt' => 'json',
                'start' => $startNum, //取得スタート位置
                'num' => $displayItemNum, //MEMO 検索結果の取得数指定
                'excludeTerms' => '' //MEMO 検索結果に含めないキーワード
            );

            $requestGoogleSearchUrl = $googleSearchUrl . http_build_query($requestParams);;

            //MEMO エラー時もコンテンツを取得する
            $option = array("https" => array('ignore_errors' => true));
            $responseJson = file_get_contents($requestGoogleSearchUrl, false, stream_context_create($option));
            $convertedResponseArray = json_decode($responseJson, true);

            $searchInfo = [];
            if (isset($convertedResponseArray['searchInformation'])) {
                $searchInfo = $convertedResponseArray['searchInformation'];
            }

            if(empty($searchInfo)){
                return ;
            }
            $searchInfos[] = $searchInfo;

            $responseItems = [];
            if (isset($convertedResponseArray['items'])) {
                $responseItems = array_reduce($convertedResponseArray['items'], function ($prev, $item) {
                    return [
                        ...$prev,
                        collect($item)
                    ];
                }, []);
            }

            if(empty($responseItems)){
                return ;
            }
            $displayItems[] = $responseItems;

            $startNum = $startNum + $displayItemNum;
        }
    }
        $requestPageNum = $request['page'] ? $request['page'] : 1;
        $collectedResponseItems = new LengthAwarePaginator(collect($displayItems)->get($requestPageNum - 1) , count($displayItems), 1, $requestPageNum);

        return view('google-search.index', compact('searchInfos', 'collectedResponseItems', 'searchRequest', 'page'));
    }
}

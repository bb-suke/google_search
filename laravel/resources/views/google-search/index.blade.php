<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>google-search</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"></head>
<body>
    <main>
        <div class="container">
            <h1 class="pt-5">google検索</h1>
            <div class="pt-5">
                <form action="{{ route('google-search.index')}}" method="GET" class="form-horizontal">
                    <div class="input-group">
                        <input type="text" placeholder="検索したい内容を入力してください" class="form-control" name="search_request" value={{ $searchRequest }}>
                        <div class="input-group-btn">
                            <button type="submit" class="btn btn-outline-secondary">
                                検索
                            </button>
                        </div>
                        <div class="input-group-btn">
                            <button class="btn btn-outline-secondary">
                                <a href="{{ route('google-search.index') }}" class="text-decoration-none text-reset">
                                    クリア
                                </a>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div>
                <div class="d-flex mt-3">
                    @isset($searchInfos[$page - 1]['formattedTotalResults'])
                        <p class="fs-6 text-secondary">約 {{ $searchInfos[$page - 1]['formattedTotalResults']}} 件</p>
                    @endisset
                    @isset($searchInfos[$page - 1]['formattedSearchTime'])
                        <p class="fs-6 text-secondary">（ {{ $searchInfos[$page - 1]['formattedSearchTime'] }} 秒）</p>
                    @endisset
                </div>

                @isset($collectedResponseItems)
                    @foreach ($collectedResponseItems as $item)
                        <div class="d-flex flex-column border-bottom pt-3 pb-3">
                            <a href={{$item['link']}} class="text-decoration-none">
                                <span class="search_items_top">
                                    <h2>{{$item['title']}}</h2>

                                    @isset($item['snippet'])
                                        <h3 class="text-secondary pt-3">{{$item['snippet']}}</h3>
                                    @endisset
                                </span>
                                <span class="search_items_bottom">
                                    @isset($item['pagemap']['cse_thumbnail'][0]['src'])
                                        <img src={{$item['pagemap']['cse_thumbnail'][0]['src']}} class="pt-3" alt="サムネイル" style="width: 240px; height=120px;">
                                    @endisset
                                </span>
                            </a>
                        </div>
                    @endforeach

                    <div class="pt-5">
                        {{ $collectedResponseItems->appends(['search_request' => $searchRequest])->links() }}
                    </div>
                @endisset
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>

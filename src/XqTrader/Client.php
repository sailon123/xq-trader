<?php


namespace XqTrader;


class Client
{
    use HttpRequest;

    protected $basePath = "/v5/stock";

    protected $url = 'https://stock.xueqiu.com';
    protected $path = [
        "getStockInfo" => "/quote.json",
        "getPanKou" => "/realtime/pankou.json",
        "getKlineMinute" => "/chart/minute.json",
        "getKline" => "/chart/kline.json",
        "getKlineRange" => "/chart/kline.json",
        "getRecentTrade" => "/history/trade.json",
    ];

    protected array $defaultHeader = [
        "User-Agent" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
        "Pragma" => "no-cache",
        "Connection" => "keep-alive",
        "Accept" => "*/*",
        "Accept-Encoding" => "gzip, deflate, br",
        "Accept-Language" => "zh-CN,zh;q=0.9,en;q=0.8",
        "Cache-Control" => "no-cache",
        "Referer" => "https://xueqiu.com/P/ZH004612",
        "X-Requested-With" => "XMLHttpRequest",
    ];

    public function __construct($cookie, $options = [])
    {
        $this->headers["Cookie"] = $cookie;
        $this->headers = array_merge($this->headers, $options['headers'] ?? []);
    }

    public function setCookies($cookies)
    {
        $this->headers["Cookie"] = $cookies;
    }

    public function isResponseSuccess($response): bool
    {
        if (!isset($response['error_code'])) {
            return false;
        }
        return $response['error_code'] == 0;
    }

    public function getResponseMessage($response): string
    {
        if (!isset($response['error_description'])) {
            return "";
        }
        return $response["error_description"];
    }

    public function getHeader()
    {
        return array_merge($this->defaultHeader, $this->headers);
    }

    public function getUri($path)
    {
        return $this->url . $this->basePath . $path;
    }

    public function getStockInfo($symbol)
    {
        $query = [
            "symbol" => $symbol,
            "extend" => "detail",
        ];
        return $this->get($this->getUri($this->path[__FUNCTION__]), $query);
    }

    public function getPanKou($symbol)
    {
        $query = [
            "symbol" => $symbol,
        ];
        return $this->get($this->getUri($this->path[__FUNCTION__]), $query);
    }

    public function getKlineMinute($symbol, $period = ChartPeriod::MINUTE, $isRaw = false)
    {
        $query = [
            "symbol" => $symbol,
            "period" => $period,
        ];
        $response = $this->get($this->getUri($this->path[__FUNCTION__]), $query);
        if (!$isRaw && $this->isResponseSuccess($response)) {
            $response = $this->formatChartItem($response);
        }
        return $response;
    }

    public function getKline($symbol, $begin, $period = ChartPeriod::DAY, $isRaw = false,
                             $indicator = "kline,pe,pb,ps,pcf,market_capital,agt,ggt,balance",
                             $count = "-284", $type = "before")
    {
        $query = [
            "symbol" => $symbol,
            "begin" => $begin,
            "period" => $period,
            "indicator" => $indicator,
            "count" => $count,
            "type" => $type
        ];
        $response = $this->get($this->getUri($this->path[__FUNCTION__]), $query);
        if (!$isRaw && $this->isResponseSuccess($response)) {
            $response = $this->formatChartItem($response);
        }
        return $response;
    }

    public function getKlineRange($symbol, $begin, $end, $period = ChartPeriod::DAY, $isRaw = false,
                                  $indicator = "kline",
                                  $type = "before")
    {
        $query = [
            "symbol" => $symbol,
            "begin" => $begin,
            "end" => $end,
            "period" => $period,
            "indicator" => $indicator,
            "type" => $type
        ];
        $response = $this->get($this->getUri($this->path[__FUNCTION__]), $query);
        if (!$isRaw && $this->isResponseSuccess($response)) {
            $response = $this->formatChartItem($response);
        }
        return $response;
    }

    public function getRecentTrade($symbol, $count = 10)
    {
        $query = [
            "symbol" => $symbol,
            "count" => $count
        ];
        return $this->get($this->getUri($this->path[__FUNCTION__]), $query);
    }

    protected function formatChartItem($response)
    {
        $items = [];
        $result = [];
        $result["symbol"] = $response["data"]["symbol"];
        $column = $response["data"]["column"];
        foreach ($response['data']['item'] as $item) {
            $item = array_combine($column, $item);
            $items[] = $item;
        }
        $result['item'] = $items;
        $response['data'] = $result;
        return $response;
    }
}
<?php


class XqClientTest extends \PHPUnit\Framework\TestCase
{
    protected $cookies = "s=di11xaojot; cookiesu=281694447555510; device_id=02e9e5105707187692a3ebf043d62941; u=281694447555510; Hm_lvt_1db88642e346389874251b5a1eded6e3=1702394546; xq_a_token=9eb4932f3197a06d242009fa2ee386ce66c8799f; xqat=9eb4932f3197a06d242009fa2ee386ce66c8799f; xq_r_token=d7a7d3c7d70d9e116b3f0277feb01c5b9543559b; xq_id_token=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1aWQiOi0xLCJpc3MiOiJ1YyIsImV4cCI6MTcwNTg4NDA0OSwiY3RtIjoxNzAzODU1MjQxNTY2LCJjaWQiOiJkOWQwbjRBWnVwIn0.b8Bj9X1VQvgJVjsb-bFVEplqhHtDyZlvOHbs0WUjJDwaxLCLgsCUPgohJ26WP-TUtuW8wF4PsFgaFew7xXMgnXTWsuShFGO4J9fOAIVLJDrkN7PeqB15bHb7GUo5pz_9UeFPnAtGd1bt62uZN73KBWjg5_i5jfax33JRPwj2ZkTWZj6IABVaEdMEEXbtxkNazeusUVD3HlB9-Ncmgmn8Aub4hYQdAG9I5vvH8NUPUoG74qgdZuj3Xi0eCfLozt9HvUCPsylQyhzZrlDypiJpy2BCjeEIi6hwFwJZIKErM4iD_UnLDxbNItZEXCWaFidLLGdJForux20_vkjm6OJ9QQ; is_overseas=0; Hm_lpvt_1db88642e346389874251b5a1eded6e3=1704092665";

    public function test_can_get_stock_info()
    {
        $stockCode = "SH600036";
        $client = new \XqTrader\Client($this->cookies);
        $response = $client->getStockInfo($stockCode);
        $this->assertSame($response['error_code'], 0);
    }

    public function test_can_get_pan_kou()
    {
        $stockCode = "SH600036";
        $client = new \XqTrader\Client($this->cookies);
        $response = $client->getPanKou($stockCode);
        $this->assertSame($response['error_code'], 0);
    }

    public function test_can_get_kline_minute()
    {
        $stockCode = "SH600036";
        $client = new \XqTrader\Client($this->cookies);
        $response = $client->getKlineMinute($stockCode);
        $this->assertSame($response['error_code'], 0);
    }

    public function test_can_get_kline_minute_5d()
    {
        $stockCode = "SH600036";
        $client = new \XqTrader\Client($this->cookies);
        $response = $client->getKlineMinute($stockCode, \XqTrader\ChartPeriod::MINUTE_5_DAY);
        $this->assertSame($response['error_code'], 0);
    }

    public function test_can_get_kline()
    {
        $stockCode = "SH600036";
        $client = new \XqTrader\Client($this->cookies);
        $response = $client->getKline($stockCode, 1704186863001, \XqTrader\ChartPeriod::DAY);
        $this->assertSame($response['error_code'], 0);
    }

    public function test_can_get_kline_week()
    {
        $stockCode = "SH600036";
        $client = new \XqTrader\Client($this->cookies);
        $response = $client->getKline($stockCode, 1704186863001, \XqTrader\ChartPeriod::WEEK);
        $this->assertSame($response['error_code'], 0);
    }

    public function test_can_get_kline_month()
    {
        $stockCode = "SH600036";
        $client = new \XqTrader\Client($this->cookies);
        $response = $client->getKline($stockCode, 1704186863001, \XqTrader\ChartPeriod::MONTH);
        $this->assertSame($response['error_code'], 0);
    }

    public function test_can_get_raw_kline()
    {
        $stockCode = "SH600036";
        $client = new \XqTrader\Client($this->cookies);
        $response = $client->getKline($stockCode, 1704186863001, \XqTrader\ChartPeriod::DAY, true);
        $this->assertSame($response['error_code'], 0);
    }

    public function test_can_get_kline_range()
    {
        $stockCode = "SH600036";
        $client = new \XqTrader\Client($this->cookies);
        $response = $client->getKlineRange($stockCode, 1704186863001, 1704101614543, \XqTrader\ChartPeriod::DAY, true);
        $this->assertSame($response['error_code'], 0);
    }

    public function test_can_get_recent_trade()
    {
        $stockCode = "SH600036";
        $client = new \XqTrader\Client($this->cookies);
        $response = $client->getRecentTrade($stockCode, 16);
        $this->assertSame($response['error_code'], 0);
    }
}
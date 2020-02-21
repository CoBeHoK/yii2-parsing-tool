<?php
namespace app\commands;

use yii\console\Controller;
use GuzzleHttp\Client;
use app\models\Site;
use app\models\Coupon;

/**
 * Controller that parses sites and coupons
 */
class ParseController extends Controller{

    /**
     * Date validator
     * @param $date
     * @param string $format
     * @return bool
     */
    function validateDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    /**
     * @param $website
     */
    public function actionSites($website) {
        $client = new Client();
        // sending request to $website
        $res = $client->request('GET', $website);
        // getting data between opening and closing tags body
        $body = $res->getBody();
        // connecting to phpQuery
        $document = \phpQuery::newDocumentHTML($body);
        //Looking html, finding external class of list and reading it by 'find' command
        $news = $document->find(".item");

        foreach ($news as $new)
        {
            $pq = pq($new);
            $link = $pq->find("a");
            $pq2 = pq($link);
            $link = $pq2->attr('href');
            $text = $pq2->text();
            $model = new Site;
            $model->link = $link;
            $model->name = $text;
            $model->error = false;
            if ($model->save()) {
                echo "Site" .$link . " ( ". $text . ') successfully saved\n';
            }
        }
        echo "News: " . $news .'\n';
    }

    /**
     * @param $website
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionCoupons($website) {
        $client = new Client();
        // sending request to $website
        $res = $client->request('GET', $website);
        // getting data between opening and closing tags body
        $body = $res->getBody();
        // connecting to phpQuery
        $document = \phpQuery::newDocumentHTML($body);
        //Looking html, finding external class of list and reading it by 'find' command
        $coupons = $document->find(".coupons-detail-box.hide-on-mobile");
        /*For all the coupons of the store we will uncheck the actuality of coupon*/
        Coupon::updateAll(['actual' => 0], "(actual = 1) AND (site = '".$website."')");
        foreach ($coupons as $coupon) {
            $model = new Coupon();
            $pq = pq($coupon);
            $link = $pq->find("img");
            if ($link->size()) { /*saving image file to the assets*/
                $url = $link->attr("src");
                $filename = basename(parse_url($url, PHP_URL_PATH));
                $path = 'assets/images/' . $filename;;
                file_put_contents($path, file_get_contents($url));
                $model->img = $path;
            }
            $model->site = $website;
            $model->title = $pq->find(".couponTitle")->text();
            $model->text = $pq->find(".coupon-description")->text();
            $date = trim($pq->find(".expire-row div span:first")->text());
            if ($this->validateDate($date)) {
                $model->date = $date;
            }
            else {
                continue;
            }
            $findquery = Coupon::find()->where(['title' => $model->title, 'text'=> $model->text, 'date' =>$model->date]);
            if (!($findquery->exists())) {
                /*if it is a new coupon setting actuality*/
                $model->actual = true;
                $model->save();
            }
            else {
                /*if it is existing coupon setting actuality */
                $findmodel = $findquery->One();
                $findmodel->actual = true;
                $findmodel->update();
            }
        }
        /*Deleting all not actual coupons*/
        Coupon::deleteAll("(actual = 0) AND (site = '".$website."')");
    }

    /**
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionAll() {
        $webSites = Site::find()->all();
        /*check whethere was an error during last parsing*/
        $errorWebsite = Site::find()->where(['error' => true])->one();
        if($webSites){
            foreach ($webSites as $website){
                if ($errorWebsite != null) {
                    if ($website->error != true) {
                        /*skip correctly parsed stores*/
                        continue;
                    }
                    else {
                        $website->error = false;
                        $website->save();
                        /*continue from the error store*/
                        $errorWebsite = Site::find()->where(['error' => true])->one();
                    }
                }
                $website->error = true;
                $website->save();
                $this->actionCoupons($website->link);
                echo "website ". $website->link ." was parsed\r\n";
                $website->error = false;
                $website->save();
            }
        }
    }
}


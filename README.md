## Simple TronScan API wrapper

- Monitor Specific *Address* Transaction and transfers

- Monitor Specific *Transaction* and transfers

##### installition

using Composer `composer create-project blackpanda/tronscan`

its doesn't need to add Service Proviver to `confing/app.php`


##### Usage


read this document for more information [TronScan Api Docs](https://github.com/tronscan/tronscan-frontend/blob/dev2019/document/api.md)
```
<?PHP
	
    $tronScan = new BlackPanda\TronScan\TronScan();
    $address = 'Your Tron Network Address';
    
    /*
    * get Address information
    */
    $tronScan->getAccount($address);

    /*
    * get transactions of specific address
    */
    $tronScan->getAccountTransactions($address);
    
    ...

?>
```

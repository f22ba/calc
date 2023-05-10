<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>電卓っぽい‼を、目指した電卓</title>
    <!--css読込-->
    <link rel="stylesheet" href="calc.css">
</head>
<body>
    
    <?php
    // 結果表示のtextareaの未定義変数のエラーを非表示
    error_reporting(E_ERROR | E_PARSE | E_NOTICE);
    // 表示されてる数字 変数disnum
    $disnum=$_POST["disnum"];
    // 符号押す前までに表示されていた数字 変数stocknum
    $stocknum=$_POST["stocknum"];

    // 次の項の入力モードon/off(符号押下後数字キー押下時にonのとき表示リセット、offのとき続けて結合して表示)
    $nextinput=$_POST["nextinput"];

    // onのとき符号押下で計算して表示 off(1回目の符号押下)のときは計算しない
    $calcready=$_POST["calcready"];
    // 計算時に使用する符号の記憶
    $nextope=$_POST["nextope"];
    // 履歴確定用 削除用
    $rireki=$_POST["rireki"];
    // 履歴途中用
    $pre=$_POST["pre"];
    // ドット確認用
    $dots=$_POST["dots"];
    // $del=$_POST["del"];
    // if($del=="del"){echo $del;}

    //--------------------------------------------------------------------------
    // 数字が押下されたときの処理（連続押下の場合は結合していく）
    if(isset($_POST["number"])){
        // 表示が0なら表示を""にする そのあと数字結合 $disnumが0.の場合falseにしたいので=="0"でなく==="0"表記
        // =="0"だと小数0.以降入力が""で初期化され表示できないので注意
        // $nextinput==on(符号押下直後)は表示リセット
        
        if($disnum==="0"||$nextinput==="on"){$disnum="";}
        
        $disnum =$disnum.$_POST["number"];
        $nextinput="off";
        $dots="none";
        
        // 動作確認用
        // echo $nextinput."<br>";
        // echo $calcready."<br>";
        // echo $stocknum."<br>";
        // echo $disnum."<br>";
        // echo $nextope."<br>";
        
    }
    
    // AC押下で表示(disnum)とstocknumを""にする
    if(isset($_POST["AC"])){$calcready = "off";$disnum=0;$stocknum="";}
    
    // %押下で百分率表示(/100する)
    if(isset($_POST["%"])){$disnum/=100;}
    
    // .押下で.を数字に結合する
    // 既に表示に.が含まれる場合は結合しない strpos($***,"---")で***に---が含まれているとint位置を返し含まれていないならfalse返す
    if(isset($_POST["dot"])){
        if(false !== strpos($disnum,"."))
        // 含まれているときの処理
        {}
        // 含まれていないときの処理
        else{$dots="existence";
            $nextinput="off";
            $disnum =$disnum.$_POST["dot"];}}
        // 動作確認用
        // echo $nextinput."<br>";
        // echo $calcready."<br>";
        // echo $stocknum."<br>";
        // echo $disnum."<br>";
        // echo $nextope."<br>";
    
    // --------------------------------------------------------------------------------------------------
    // 符号押下時の処理 1回目押下時(calcready=""のとき)は計算しない 次の演算子を代入 2回目押下以降は計算して表示
    // 符号押下時の処理 "="押下もこの処理に入る
    if(isset($_POST["ope"])){
        // 小数点入力直後に符号押下で小数点消す
        if($dots=="existence"){
            $disnum=substr($disnum,0,-1);
            $dots="none";
        }
        // 1回目符号押下時(=押下後または1回目符号押下後かつ数字押下してある)
        if($calcready!=="on"&&$nextinput=="off"){
            
            switch($_POST["ope"]){
                case "+":
                    $nextope="+";
                    $pre=$disnum;
                    break;
                case "-":
                    $nextope="-";
                    $pre=$disnum;
                    break;
                case "*":
                    $nextope="*";
                    $pre=$disnum;
                    break;
                case "/":
                    $nextope="/";
                    $pre=$disnum;
                    break;
            }
        }else
        // "="押下時かつ被演算子が片方しか入力されていない場合、片方の数値のみ表示　履歴実装ならここで計算ストック消す
            {
            if($_POST["ope"]=="=" && $nextinput==="on"){
            }
            // 2回目以降符号押下時かつ被演算子2つある場合、計算して表示
            elseif($nextinput=="off"){
                switch($nextope){
                    case "+":
                        $pre.="+";
                        $pre.="$disnum";
                        $disnum=$stocknum+$disnum;
                        break;
                    case "-":
                        $pre.="-";
                        $pre.="$disnum";
                        $disnum=$stocknum-$disnum;
                        break;
                    case "*":
                        $pre.="*";
                        $pre.="$disnum";
                        $disnum=$stocknum*$disnum;
                        break;
                    case "/":
                        $pre.="/";
                        $pre.="$disnum";
                        if($disnum!=="0"){
                            $disnum=$stocknum/$disnum;
                        break;
                        }else{$disnum="0では割れません.";}
                }
            }
        }
        if($disnum=="0では割れません."&&$nextinput=="on"){$disnum="0";}//0では割れません表示の符号押下時は表示0にする
        
        //次の演算子代入(1回符号+-*/押下済み)
        if($calcready=="on"){
            // =押下後に表示を利用して計算するためにpreにdisnumを入れる
            if($pre==""){
                // =押下後に表示を利用せずに新たに数字を押下して新しい計算をはじめるためにope"="のときはpreに入れない。
                if($_POST["ope"]!=="="){
                    $pre=$disnum;
                }
                // 動作確認用
                // echo $_POST["ope"];
            }
            switch($_POST["ope"]){
                case "+":
                    $nextope="+";
                    break;
                case "-":
                    $nextope="-";
                    break;
                case "*":
                    $nextope="*";
                    break;
                case "/":
                    $nextope="/";
                    break;
                default:
                    // nextope!==""で履歴表示"="連打対策のif文 1回しか入らない
                    if($nextinput=="off"&&$calcready=="on"&&$nextope!==""){
                        $pre.="=";
                        $rireki.=$pre.="$disnum"."<br>";
                        $pre="";
                        $nextinput="on";
                    }
                    $nextope="";//=押下時にnextopeを""にすることで=連打対策
                    
            }
        }
        $stocknum = $disnum;
        if($nextope==""){}
        // 符号+-*/を押したときelseに入る
        else{
            $nextinput = "on";
            $calcready = "on";
        }
        
        // 動作確認用
        // echo $nextinput."<br>";
        // echo $calcready."<br>";
        // echo $stocknum."<br>";
        // echo $disnum."<br>";
        // echo $nextope."<br>";
        // $rireki="$pre"."<br>";
        // echo $rireki;
    }
    ?>
    <div class="main_width">
        <div class="wrapper">
            <form class="calculator" method="post" action="calctest3.php">
                
                <!-- php変数呼び出し用hidden属性で非表示 計算結果表示はtextでデザイン作成してしまったので…そのままエラー非表示に… -->
                <input type="hidden" name="stocknum" value="<?php echo $stocknum;?>">
                <input type="hidden" name="nextinput" value="<?php echo $nextinput;?>">
                <input type="hidden" name="calcready" value="<?php echo $calcready;?>">
                <input type="hidden" name="nextope" value="<?php echo $nextope;?>">
                <input type="hidden" name="rireki" value="<?php echo $rireki;?>">
                <input type="hidden" name="pre" value="<?php echo $pre;?>">
                <input type="hidden" name="dots" value="<?php echo $dots;?>">
                
                

                <!-- 計算結果表示 初期0表示 値(disnum)保持の際はその値をvalue属性に埋めこんで表示させる-->
                <input type="text" readonly class="result" name="disnum" 
                value="<?php if(empty($disnum)){$disnum = 0; echo 0;} else {echo $disnum;}?>">

                <!-- 電卓キーの配置はbuttonデザインはダサいのでspanタグでCSSデザイン -->
                <!-- submitにするならinput type buttonにする必要ないと思うのと、buttonタグなら疑似要素(マウスでホバー表現)ができるから -->
                <span class="num clear">
                    <button name="AC" value="AC">AC</button>
                </span>
                <span class="num">
                    <button name="%" value="%">%</button>
                </span>
                <span class="num">
                    <button name="ope" value="/">/</button>
                </span>
                <span class="num">
                    <button name="number" value="7">7</button>
                </span>
                <span class="num">
                    <button name="number" value="8">8</button>
                </span>
                <span class="num">
                    <button name="number" value="9">9</button>
                </span>
                <span class="num">
                    <button name="ope" value="*">＊</button>
                </span>
                <span class="num">
                    <button name="number" value="4">4</button>
                </span>
                <span class="num">
                    <button name="number" value="5">5</button>
                </span>
                <span class="num">
                    <button name="number" value="6">6</button>
                </span>
                <span class="num">
                    <button name="ope" value="-">－</button>
                </span>
                <span class="num">
                    <button name="number" value="1">1</button>
                </span>
                <span class="num">
                    <button name="number" value="2">2</button>
                </span>
                <span class="num">
                    <button name="number" value="3">3</button>
                </span>
                <span class="num">
                    <button name="ope" value="+">+</button>
                </span>
                <span class="num zero">
                    <button name="number" value="0">0</button>
                </span>
                <span class="num">
                    <button name="dot" value=".">.</button>
                </span>
                <span class="num equal">
                    <button name="ope" value="=">=</button></span>
                
                
            </form>
        </div>
        <form method="post" action="calctest3.php">
            <div class="rireki_del">
                <p>
                <span class="del">
                <button name="del" value="del">履歴削除</button>
                </span>
                </p>
            </div>
            
        </form>
        
        <p class="rireki"><?php echo $rireki; ?></p>
        
    </div> 
    
</body>
</html>
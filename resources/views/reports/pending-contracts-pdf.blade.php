
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<HTML>
<HEAD>
<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
<TITLE> {{ ucfirst(@request()->t) }} Contracts </TITLE>
<META name="generator" content="BCL easyConverter SDK 5.0.252">

</HEAD>

<BODY>

  <footer>
       Page <span class="pagenum"></span>
  </footer>

  <header > 
      

</header>

<DIV id="page_1">
      <div style="text-align:center; width: 100%; margin-bottom: 10px;margin-left: 75px;">
        <P style="text-align:center;" class="p0 ft0"> {{ @$project->name }} Pending Contracts</P>
    </div>

    <!-- <div class="right1">
    
    </div> -->

<div>

<!-- <div class="id1_1">
  
</div> -->

<DIV id="id1_2">

<TABLE style="margin-left: 175px;" cellpadding=0 cellspacing=0 class="t3">
<TR>
  <TD class="lb tr4 td2"><P class="p5 ft7">A</P></TD>
  <TD class="tr4 td2"><P class="p5 ft8">B</P></TD>
</TR>
<TR>
  <TD class="lb tr5 td2"><P class="p12 ft7">Category </P></TD>
  <TD class="tr5 td2"><P class="p5 ft8">Trade</P></TD>
</TR>

    @foreach($categories as $k => $cat)

         @php   
          $catTrades = @$trades->where('category_id', $cat->id);
          $end = ($k == ($categories->count() - 1)) ? true : false;
          $i = 0;
         @endphp

        
        <TR>
          <TD class="lb tr1 td4"><P class="p5 ft7"><b>{{ $cat->name }}</b></P></TD>
          <TD class="tr1 td5 max-width-td" > </TD>
        </TR>
         
        @foreach($catTrades as $trd)
             @php
             $bb = ($end && $i == ($catTrades->count() - 1)) ? 'bb' : '';
             $i++;
            @endphp
            <TR>
              <TD class="lb tr1 td4 {{ $bb }}"><P class="p5 ft7"></P></TD>
              <TD class="tr1 td4 {{ $bb }}"><P class="p5 ft7">{{ $trd->name  }} {{ $i}}</P></TD>
            </TR>

          @endforeach
         
   @endforeach


</TABLE>


</DIV>

</DIV>
</DIV>


</BODY>
<STYLE type="text/css">

body {margin-top: 0px;margin-left: 0px;}

#page_1 {    
    position: relative;
    margin: -15px auto;
    padding: 0px;
    border: none;
    width: 1056px;
    height: 731px;
  }

#page_1 #id1_1 {
    float: left;
    border: none;
    padding: 0px;
    border: none;
    width: 641px;
    overflow: hidden;
  }

#page_1 #id1_2 {    
    margin: 30px 0px 0px;
    padding: 0px;
    border: none;
    overflow: hidden;
  }

.dclr {clear:both;float:none;height:1px;margin:0px;padding:0px;overflow:hidden;}

.ft0{font: bold 23px 'Arial';line-height: 27px;}
.ft1{font: 15px 'Arial';line-height: 17px;}
.ft2{font: bold 20px 'Arial';line-height: 24px;}
.ft3{font: 18px 'Arial';line-height: 23px;}
.ft4{font: 18px 'Arial';line-height: 22px;}
.ft5{font: 1px 'Arial';line-height: 1px;}
.ft6{font: bold 24px 'Arial';line-height: 29px;}
.ft7{font: 13px 'Arial';line-height: 16px;}
.ft8{font: 12px 'Arial';line-height: 15px;}
.ft9{font: 11px 'Arial';line-height: 14px;}
.ft10{font: 1px 'Arial';line-height: 15px;}
.ft11{font: 13px 'Arial';line-height: 15px;}
.ft12{font: 1px 'Arial';line-height: 7px;}
.ft13{font: 1px 'Arial';line-height: 8px;}
.ft14{font: 1px 'Arial';line-height: 9px;}
.ft15{font: bold 12px 'Arial';line-height: 15px;}
.ft23{font: bold 12px 'Arial';line-height: 15px;}
.ft21{font: bold 12px 'Arial';line-height: 15px;}

.p0{text-align: right;padding-right: 110px;margin-top: 0px;margin-bottom: 0px;}
.p1{text-align: left;padding-left: 20px;margin-top: 10px;margin-bottom: 0px;}
.p2{text-align: left;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
.p3{text-align: left;padding-left: 7px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
.p4{text-align: left;padding-left: 4px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
.p5{text-align: center;padding-right: 2px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
.p6{text-align: left;padding-left: 29px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
.p7{text-align: center;padding-right: 13px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
.p8{text-align: center;padding-right: 18px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
.p9{text-align: center;padding-right: 16px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
.p10{text-align: left;padding-left: 60px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
.p11{text-align: center;padding-right: 4px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
.p12{text-align: center;padding-right: 1px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
.p13{text-align: left;padding-left: 35px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
.p14{text-align: center;padding-right: ;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
.p15{text-align: center;padding-right: 12px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
.p16{text-align: center;padding-right: 17px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
.p17{text-align: center;padding-right: 10px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
.p18{text-align: center;padding-left: 3px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
.p19{text-align: center;padding-right: 14px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
.p20{text-align: center;padding-right: 43px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
.p21{text-align: left;padding-left: 20px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}
.p22{text-align: left;padding-left: 27px;margin-top: 0px;margin-bottom: 0px;white-space: nowrap;}

.td0{padding: 0px;margin: 0px;width: 200px;vertical-align: bottom;}
.td1{padding: 0px;margin: 0px;width: 175px;vertical-align: bottom;}
.td2{padding: 0px;margin: 0px;width: 332px;}
.td3{padding: 0px;margin: 0px;width: 222px;vertical-align: bottom;}
.td4{padding: 0px;margin: 0px;width: 43px;vertical-align: middle;}
.td5{padding: 0px;margin: 0px;width: 194px;vertical-align: middle;}
.td6{padding: 0px;margin: 0px;width: 103px;vertical-align: middle;}
.td7{padding: 0px;margin: 0px;width: 104px;vertical-align: middle;}
.td8{padding: 0px;margin: 0px;width: 96px;vertical-align: middle;}
.td9{padding: 0px;margin: 0px;width: 97px;vertical-align: middle;}
.td10{padding: 0px;margin: 0px;width: 91px;vertical-align: middle;}
.td11{padding: 0px;margin: 0px;width: 56px;vertical-align: middle;}
.td12{padding: 0px;margin: 0px;width: 95px;vertical-align: middle;}
.td13{padding: 0px;margin: 0px;width: 200px;vertical-align: middle;}
.td14{padding: 0px;margin: 0px;width: 497px;vertical-align: middle;}
.td22{padding: 0px;margin: 0px;width: 196px;vertical-align: bottom;}
.td23{padding: 0px;margin: 0px;width: 165px;vertical-align: bottom;}

.tr0{height: 30px;}
.tr1{height: 24px;}
.tr2{height: 26px;}
.tr3{height: 34px;}

.tr4{height: 23px;}
.tr5{height: 22px;}
.tr6{height: 15px;}
.tr7{height: 7px;}
.tr8{height: 8px;}
.tr9{height: 16px;}
.tr10{height: 17px;}
.tr11{height: 9px;}
.tr12{height: 24px;}
.tr13{height: 24px;}
.tr14{height: 62px;}
.tr15{height: 25px;}
.tr18{height: 20px;}
.tr19{height: 30px;background: #ededed;}
.tr20{height: 31px;}
.tr-grey{
  background: #ededed;
}

.t0{
  width:  590px;
  margin-left: -1px;
  margin-top: 10px;
  font: bold 20px 'Arial';
  border: 4px solid #17aecf;
}

/*.t1{
  width: 550px;
  margin-left: 1px;
  margin-top: -7px;
  font: 16px 'Arial';
}

.t2{width: 430px;font: 13px 'Arial';}
*/
.t3{font: 16px 'Arial';}

.left1{
  border: 4px solid #17aecf;
  width: 580px;
  padding: 10px 0px;
  position: relative;
}

.right1{
    float: left;
    border: 4px solid #17aecf;
    width: 350px;
    padding-left: 10px;
    margin-bottom: 12px;
}

.table1 td{
    padding: 0px 5px;
}

.table2 td.bwr{
    border: 2px solid;
    border-right: none;
}

.table2 td.bwl{
    border: 2px solid;
    border-left: none;
}

.table2 td.btb{
    border: 2px solid;
    border-left: none;
    border-right: none;
}

.table2 td.bl{
    border-left:2px solid;
}

.table2 td.blt{
    border-left:2px solid;
     border-top:2px solid;
}
.table2 td.blb{
    border-left:2px solid;
    border-bottom:2px solid;
}

.table2 td.br{
    border-right:2px solid;
}

.table2 td.brt{
    border-right:2px solid;
    border-top:2px solid;
}

.table2 td.brl{
    border-right:2px solid;
    border-bottom:2px solid;
}    

.table2 td.bt{
    border-top:2px solid;
}
.table2 td.bb{
    border-bottom:2px solid;
}
.table2 tr{
  width: 400px;
}

.left1 img{
  width: 120px;
  position: absolute;
  /*height: 40px;*/
  top: 5px;
  left: 20px;
}
.left1 .ft1{
  font-size: 12px;
}


footer {
  position: fixed; 
  bottom:-10px;
  text-align: right;
  font-size: 12px;
}


header {
    /*position: relative;*/

    position: fixed;
    top: 0px;
    left: 0px;
    right: 0px;
    height: 50px;
    text-align: center;
    line-height: 35px;
    /*display: none;*/
}

.pagenum:before {
      content: counter(page);
}
.lb{
    border-left: 1px solid !important;
}

.lrb{
    border-left: 1px solid !important;
    border-right: 1px solid !important;
}

.lr{
    border-right: 1px solid !important;
}
.bb{
    border-bottom: 1px solid !important;
}
.pbb{
    padding-top: 7px;
    border-bottom: 1px solid !important;

}
.pbb span{
    margin-bottom: 7px;
    display: inline-block;
    margin-left: 25px;
}
.pbb2{
    padding-top: 7px;
    border-bottom: 1px solid !important;
}
.pbb2 span{
    margin-bottom: 7px;
    display: inline-block;
    margin-left: 25px;
}
.bub{
    border-bottom: 1px solid !important;
    border-top: 1px solid !important;
}
.brn{
    border-right: none !important;
}

.blrn{
    border-left: 1px solid !important;
    border-right: none !important;
}

.t3 td.tr4{
border: 1px solid;
border-bottom: none;
border-left: none;
}

.t4 td.tr19{
border: 1px solid;
border-bottom: none;
border-left: none;
}
.t4 td.tr20{
border: 1px solid;
border-bottom: none;
border-left: none;
}
.t3 td.tr5{
border: 1px solid;
border-bottom: none;
border-left: none;
}
.t3 td.tr1{border-top: 1px solid;
border-right: 1px solid;
}

.t3 td.tr13{
border-top: 1px solid;
border-right: 1px solid;
}

.star{
  color: red;
}

p.p23 {
    padding-left: 6px;
    margin-top: 0px;
    line-height: 8px;
}

p.p24 {
    padding-left: 6px;
    margin-top: 0px;
    line-height: 8px;
    margin-bottom: 10px;
    width: 200px;
}
p.p32  {
    padding-right: 6px;
    margin-top: 0px;
    line-height: 8px;
    margin-bottom: 0px;
    text-align: right;
}

.t4{width: 470px;font: 13px 'Arial';margin-top: 20px;}
.t5{width: 351px;font: 20px 'Arial';}
.max-width-td{width: 200px; word-wrap: break-word;overflow: hidden;}
</STYLE>

</HTML>

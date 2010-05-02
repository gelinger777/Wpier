var pbr=new Ext.ProgressBar({
       text:''
});

var AllCount=0;
var AllPercent=0;
var AllStart=0;
var StepCount=50; // Сколько файлов за 1 шаг прятать в архив
var CurrentStepCopy=0;
var ErrRefresh=0;
var CopyUrl='';
var mess=[
'Копирование файлов',
'Копирование базы данных',
'Формирование пакетного файла'
];

function finCopyBackup() {
  frm.items.items[0].el.dom.innerHTML=mess[0]+' - OK';
  frm.items.items[2].el.dom.innerHTML=mess[1]+' - в процессе...';
  pbr.updateProgress(0);
  pbr.updateText('Чтение данных...');
  Ext.Ajax.request({
    url: '/makebackup.php?dbase=yes',
    success: function(q) {
      alert('fin:'+q.responseText);

    }
  });  
}

function CopyDB(table) {
  Ext.Ajax.request({
    url: '/makebackup.php?table='+tible,
    success: function(q) {
      window.location='/'+q.responseText;
      w.close();
    }
  });
}

function finDbCreate() {
  frm.items.items[1].el.dom.innerHTML=mess[1]+' - OK';
  frm.items.items[2].el.dom.innerHTML=mess[2]+' - в процессе...';
  pbr.updateText('Формирование пакетного файла...');
  Ext.Ajax.request({
    url: '/makebackup.php?marge=yes',
    success: function(q) {
      window.location='/'+q.responseText;
      w.close();
    }
  }); 
}

function CopyBackupFiles() {
  if(AllCount>0) {
    
    
        
    CopyUrl='/makebackup.php?cc='+CurrentStepCopy+'&start='+AllStart;
  
  //alert(CopyUrl);
    
    Ext.Ajax.request({
      url: CopyUrl,
      success: function(q) {
        CurrentStepCopy++;
        // тут нужно поставить изменение позиции прогресбара
        //alert(q.responseText);
        if(q.responseText!="" && q.responseText!="fin") {
          
          
          
          AllStart=parseInt(q.responseText);
          if(isNaN(AllStart)) {
            alert(q.responseText);             
            return false;
          }

          //AllCount-=AllStart;
          
          var p=AllStart/AllPercent;
          pbr.updateProgress(p/100);
          pbr.updateText('Архивация '+parseInt(p)+'%');

          CopyBackupFiles();
        } else if(q.responseText!='') {
          finCopyBackup();
        } else if(ErrRefresh<1) {
        // 10 раз повторяем вопрос 
          ErrRefresh++;
          CopyBackupFiles();
        } else {
          alert('Ошибка при копировании:'+q.responseText);
        }
      },
      failure: function() {alert('Ошибка при упаковке файлов');}
    });
  } else {
    finCopyBackup();
  }
} 

var frm=new Ext.FormPanel({
        labelWidth: 75, // label settings here cascade unless overridden
        bodyStyle:'padding:5px 5px 0',
        width: 380,
        defaults: {width: 330},
        defaultType: 'textfield',

        items: [
    
    new Ext.form.Label({
      text:mess[0],
      height:16
    }),new Ext.Panel({bodyBorder:false,hideBorders:true,border:false}),
    new Ext.form.Label({
      text:mess[1],
      height:16
    }),new Ext.Panel({bodyBorder:false,hideBorders:true,border:false}),
    new Ext.form.Label({
      text:mess[2],
      height:25
    }),new Ext.Panel({height:5,bodyBorder:false,hideBorders:true,border:false}),
    pbr
  
  ]
  });

var w = new Ext.Window({
  id:"ProgWin",
  title:"Создание резервной копии",
  layout:"fit",
  width:358,
  height:160,
  minimizable:false,
  maximizable:false,
  plain: true,       
  items:[frm],
  
  buttons:[{
    text:"Вперед",
    handler:function() {
      this.disable();

      frm.items.items[0].el.dom.innerHTML=mess[0]+' - в процессе...';

      // for test!
      //  finCopyBackup();
      //  return false;
      // end for test!


      pbr.updateText('Чтение каталога...');
      Ext.Ajax.request({
                      
        url: '/makebackup.php?getfiles=yes',
        success: function(q) {
          pbr.updateText('Архивация 0%');
          CurrentStepCopy=0;
          ErrRefresh=0;
          AllCount=parseInt(q.responseText);
          AllPercent=AllCount/100;
          CopyBackupFiles();
        },
        failure: function() {alert('Ошибка при чтении каталога');}
      });
    }
   },{
    text:"Закрыть",
    handler:function() {w.close();}
  }]        
 }); 
 w.show(); 
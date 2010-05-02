function FormatDates(val) {
  var dt = new Date(parseInt(val)*1000);
  return dt.format('d.m.y H:i')
}

TEXTS={
"IO_Copy":"Копирование",
"IO_Stop":"Остановить",
"IO_Progress":"(осталось: %s)",
"IO_Delete":"Удаление",
"IO_DeleteText":"Удаление файлов %s из <b>%s</b>",
"IO_Replace":"Заменить файл<br><br><b>%s:</b> %s?",
"IO_YesToAll":"Да для всех",
"IO_Yes":"Да",
"IO_Skip":"Пропуск",
"IO_SkipAll":"Пропуск всех",
"IO_Cancel":"Отменить",

"Error":"Ошибка",
"Cancel":"Отмена",

"OpenFileDlgTitle":"Открыть файл",

"IO_CopyFromTo":"Копировать %s на %s в",
"IO_CopyFiles":"Копировать файлы (%s) на %s в",
"But_OK":"Да",
"But_Cancel":"Отмена",
"IO_DelConfirm":"Удалить выделенные файлы (%s)?",
"IO_RnmPrompt":"Переименовать файл/директорию",
"IO_Load":"Идет загрузка...",
"IO_SelStore":"Сменить Store...",
"IO_EdtStoreList":"Редактирование списка Store",
"IO_AddStore":"Добавить",
"IO_Save":"Сохранить",
"IO_Delete":"Удалить",
"IO_DelStoreConfirm":"Удалить store %s?",
"IO_Close":"Закрыть",
"IO_AddFile":"Добавить файл",
"IO_NewDir":"Создать директорию",
"IO_EditStor":"Изм. Store...",
"IO_TB_Back":"Назад",
"IO_TB_Refresh":"Обновить",
"IO_TB_Del":"Удалить файл(ы)",
"IO_TB_Newdir":"Создать директорию",
"IO_TB_Copy":"Копировать выделенный файл(ы)",
"IO_TB_SelAll":"Выделить все",

"IO_ChStor_Name":"Название",
"IO_ChStor_Path":"Сетевой путь",
"IO_ChStor_Passv":"Вкл. пассивный режим (для локальных Store)",
"IO_Open":"Открыть"
};

DLG=new function() {
  this.c=function(t,v) {
    return confirm(this.t(t,v));
  }
  // Alert
  this.a=function(t,v) {
    alert(this.t(t,v));
  }
  this.e=function(t,v) {
    Ext.MessageBox.show({
       title: this.t('Error'),
       msg: this.t(t,v),
       buttons: Ext.MessageBox.OK,
       icon: "error"
     });
  }
  // Warning
  this.w=function(t,v) {
    alert(this.t(t,v));
  }
  this.p=function(t,t1,v,v1) {
    return prompt(this.t(t,v),this.t(t1,v1));
  }
  this.t=function(t,val) {
    var tx;
    try {
      eval('tx=TEXTS.'+t);
    } catch(e) {tx=t;}
    if(val!=null) {
      for(var i=0;i<val.length;i++) tx=tx.replace('%s',val[i]);
    }
    return tx;
  }
}
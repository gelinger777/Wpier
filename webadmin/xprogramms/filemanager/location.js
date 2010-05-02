function FormatDates(val) {
  var dt = new Date(parseInt(val)*1000);
  return dt.format('d.m.y H:i')
}

TEXTS={
"IO_Copy":"�����������",
"IO_Stop":"����������",
"IO_Progress":"(��������: %s)",
"IO_Delete":"��������",
"IO_DeleteText":"�������� ������ %s �� <b>%s</b>",
"IO_Replace":"�������� ����<br><br><b>%s:</b> %s?",
"IO_YesToAll":"�� ��� ����",
"IO_Yes":"��",
"IO_Skip":"�������",
"IO_SkipAll":"������� ����",
"IO_Cancel":"��������",

"Error":"������",
"Cancel":"������",

"OpenFileDlgTitle":"������� ����",

"IO_CopyFromTo":"���������� %s �� %s �",
"IO_CopyFiles":"���������� ����� (%s) �� %s �",
"But_OK":"��",
"But_Cancel":"������",
"IO_DelConfirm":"������� ���������� ����� (%s)?",
"IO_RnmPrompt":"������������� ����/����������",
"IO_Load":"���� ��������...",
"IO_SelStore":"������� Store...",
"IO_EdtStoreList":"�������������� ������ Store",
"IO_AddStore":"��������",
"IO_Save":"���������",
"IO_Delete":"�������",
"IO_DelStoreConfirm":"������� store %s?",
"IO_Close":"�������",
"IO_AddFile":"�������� ����",
"IO_NewDir":"������� ����������",
"IO_EditStor":"���. Store...",
"IO_TB_Back":"�����",
"IO_TB_Refresh":"��������",
"IO_TB_Del":"������� ����(�)",
"IO_TB_Newdir":"������� ����������",
"IO_TB_Copy":"���������� ���������� ����(�)",
"IO_TB_SelAll":"�������� ���",

"IO_ChStor_Name":"��������",
"IO_ChStor_Path":"������� ����",
"IO_ChStor_Passv":"���. ��������� ����� (��� ��������� Store)",
"IO_Open":"�������"
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
// Добавляем функцию, срабатывающую на каждом цикле объекта LOCKOBJ
MyTasksWindow=null;

LOCKOBJ.timefunctions[LOCKOBJ.timefunctions.length]=function(txt) {
  var s='taskpassive';
  if(txt.indexOf('newtask')!=-1) {
    s='taskactive';
  }
  document.getElementById('MyTasksButton').firstChild.firstChild.firstChild.nextSibling.firstChild.firstChild.className='x-btn-text '+s;
}


// Добавляем иконку в таск-бар
PLUGINS.main.tasks=new function() {
	this.init=function() {
	  TopToolbar.items.items[0].items.items[0].insertButton(3,new Ext.Button({
	    id:'MyTasksButton',
	    text:'',
	    tooltip:'Задачи',
	    iconCls:'taskpassive',
	    handler:function(){
	    if(MyTasksWindow==null) InitMyTasksWindow();
	    else MyTasksWindow.show();
	    document.getElementById('MyTasksButton').firstChild.firstChild.firstChild.nextSibling.firstChild.firstChild.className='x-btn-text taskpassive';

	      }
	  }));
	}
}


function InitMyTasksWindow() {
  var grd=null;
  var tab=null;
  MyTasksWindow=new function() {

    this.Stor=new Ext.data.Store({
        url: 'xprogramms/tasks/tasks.php',
        remoteSort: true,
        reader: new Ext.data.XmlReader({
          record: 'Item',
          id: 'id',
          totalRecords: 'TotalResults'
        }, [{name: 'id', mapping: 'ItemAttributes > id'},'id', 'status', 'task', 'action', 'comment', 'date1', 'date2','AdminName','project','fulltask','type'])
    });

    var xg=Ext.grid;

    var sm = new xg.CheckboxSelectionModel();
    //var sm2 = new xg.CheckboxSelectionModel();
    var arract=['','Отправлена','Принята','В работе','Завершена']

    var expander = new xg.RowExpander({
      tpl : new Ext.Template(
        '{fulltask}'
      )
    });


    grd=new xg.EditorGridPanel({
        id:'Tasks-grid',
        ds: this.Stor,
        clicksToEdit:1,

            /*listeners:{
          rowdblclick :function(g,i,e) {
            WINS.AdminEditWin(UsersGrid.getSelectionModel().getSelected().id);
          }
            },*/

        cm: new xg.ColumnModel([
          /*sm
          ,*/
          expander
          ,{header: 'Статус', width: 50, dataIndex: 'status', sortable:false}
          ,{header: 'Тип', width: 50,dataIndex: 'type', sortable:false}
          ,{header: 'Задача', width: 250,dataIndex: 'task', sortable:false}
          ,{header: 'Поставлена', width: 150,dataIndex: 'AdminName', sortable:false}
          ,{header: 'Проект', width: 150,dataIndex: 'project', sortable:false}
          ,{header: 'Дата начала', width: 70,dataIndex: 'date1', sortable:false}
          ,{header: 'Дата заверш.', width: 70,dataIndex: 'date2', sortable:false}
          ,{header: 'Действие', width: 150,dataIndex: 'action', sortable:false,
            renderer: function(x) {return arract[x];},
            editor: new Ext.form.ComboBox({
              editable:false,
              typeAhead: true,
              mode: 'local',
              triggerAction: 'all',
              displayField:'val',
              valueField: 'key',
              store:new Ext.data.SimpleStore({
                  fields: ['key','val'],
                  data : [['2','Принята'],['3','В работе'],['4','Завершена']]
              })
            }
          )}
          //,{header: 'Комментарий', width: 150,dataIndex: 'comment', sortable:true}
        ]),
        //sm:sm2,
        viewConfig: {forceFit:true},

        plugins: expander,
        enableColLock: false,
        loadMask: true,

        listeners:{
          afteredit:function(e) {
            Ext.Ajax.request({
              url: 'xprogramms/tasks/tasks.php?ChangeVal='+e.field,
              success: function(response) {
                // изменения сохранились
              },
              params: {
                data:escape(e.value),
                id:e.record.id
              }
            });
          }
        },


        /*bbar: new Ext.PagingToolbar({
          pageSize: 20,
          store: usrstore,
          displayInfo: true,
          displayMsg: 'Показано',
          emptyMsg: 'Нет записей'

        }),*/

        //autoWidth:true,
        //autoHeight:true,
        width:150,
	height:150,
        frame:false,
        stripeRows: true,
        border:false,
        bodyBorder:false,
        hideBorders:false,
        title:''
        //iconCls:'icon-grid'
    });


    this.Win=new Ext.Window({
      id:'MyTasksWindow',
      title:'Задачи',
      iconCls:'taskpassive',
      width:550,
      height:400,
      closeAction:'hide',
      //layout: 'border',
      border: false,
      tbar:[{
          text:'Обновить',
          tooltip: '',
          iconCls: 'refresh',
          handler:function() {
            MyTasksWindow.Stor.reload();
          }
        }
      ],
      items:[grd],
      listeners:{
        resize:function(wn,w,h) {
	  grd.setWidth(wn.getInnerWidth());
          grd.setHeight(wn.getInnerHeight());
        }
      }
    });


    this.show=function() {
      this.Win.show();
      this.Stor.reload();
    }

  }

  MyTasksWindow.Win.show();
  MyTasksWindow.Stor.load({params:{start: 0, limit: 200}});

  grd.setWidth(MyTasksWindow.Win.getInnerWidth());
  grd.setHeight(MyTasksWindow.Win.getInnerHeight());
  ///alert(Ext.getCmp('MyTasksWindow-Top').getInnerHeight());
  //grd.setHeight(Ext.getCmp('MyTasksWindow-Top').getInnerHeight());

}
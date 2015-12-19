/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     2015/12/2 23:31:34                           */
/*==============================================================*/
create database nengrongweb;
use nengrongweb;

drop table if exists ENF_Area;

drop table if exists ENF_Doc;

drop table if exists ENF_Evaluation;

drop table if exists ENF_Ground;

drop table if exists ENF_Housetop;

drop table if exists ENF_Project;

drop table if exists ENF_PushProject;

drop table if exists ENF_User;

drop table if exists ENF_Admin;

/*==============================================================*/
/* Table: ENF_Area                                              */
/*==============================================================*/
create table ENF_Area
(
   id                   varchar(10) not null comment 'id',
   area                 varchar(50) not null comment '地区描述',
   parent_id            varchar(10) not null comment '父级id',
   primary key (id)
)ENGINE = InnoDB DEFAULT CHARSET = utf8;

alter table ENF_Area comment '地区表';

/*==============================================================*/
/* Table: ENF_Doc                                               */
/*==============================================================*/
create table ENF_Doc
(
   id                   bigint not null auto_increment,
   file_name            varchar(100) not null comment '文件名称',
   file_rename          varchar(100) not null comment '文件重定向名称url',
   update_date          datetime not null comment '上传时间',
   primary key (id)
)ENGINE = InnoDB DEFAULT CHARSET = utf8;

alter table ENF_Doc comment '附件表';

/*==============================================================*/
/* Table: ENF_Evaluation                                        */
/*==============================================================*/
create table ENF_Evaluation
(
   id                   bigint not null auto_increment,
   project_id           bigint comment '归属项目id',
   IRR                  double comment '内部收益率',
   evaluation_result    varchar(10) comment '评价结果',
   static_payback_time  double comment '静态投资回收年',
   dynamic_payback_time double comment '动态投资回收期',
   LCOE                 double comment 'LCOE',
   npv                  double comment '净现值',
   power_asset_current_value double comment '电站资产累计现值',
   evaluation_content   varchar(600) comment '评价内容',
   document_review      varchar(600) comment '文件审查',
   project_quality_situation varchar(600) comment '工程建设质量和系统运行情况',
   project_invest_situation varchar(600) comment '项目建设投资情况',
   project_earnings_situation varchar(600) comment '项目经济收益情况',
   doc1                 varchar(20) comment '附件1对应的附件表id',
   doc2                 varchar(20) comment '附件2对应的附件表id',
   doc3                 varchar(20) comment '附件3对应的附件表id',
   create_date          datetime comment '创建时间',
   change_date          datetime comment '修改时间',
   status               int not null default 0 comment '状态类型：0正常、1已激活、2未激活、11未提交、12已提交未查看（业务员界面高亮处理）、13已提交已查看、21签意向合同（可以推送）、22签意向合同、31签融资合同、41已推送、42未推送、51尽职调查已保存、52尽职调查已提交、9999删除',
   primary key (id),
   INDEX `evaluation_project_id` (`project_id`)
)ENGINE = InnoDB DEFAULT CHARSET = utf8;

alter table ENF_Evaluation comment '尽职调查表';

/*==============================================================*/
/* Table: ENF_Ground                                            */
/*==============================================================*/
create table ENF_Ground
(
   id                   bigint not null auto_increment,
   project_id           bigint not null comment '归属项目id',
   picture1             varchar(100) comment '图片1URL',
   picture2             varchar(100) comment '图片2URL',
   picture3             varchar(100) comment '图片3URL',
   picture4             varchar(100) comment '图片4URL',
   picture5             varchar(100) comment '图片5URL',
   picture6             varchar(100) comment '图片6URL',
   picture7             varchar(100) comment '图片7URL',
   picture8             varchar(100) comment '图片8URL',
   picture9             varchar(100) comment '图片9URL',
   picture10            varchar(100) comment '图片10URL',
   picture11            varchar(100) comment '图片11URL',
   picture12            varchar(100) comment '图片12URL',
   contract             varchar(20) comment '合同的docID',
   ground_property      int comment '土地性质（1一般农田、2林地、3荒地、4鱼塘、5基本农田）',
   ground_area          double comment '租赁土地面积',
   rent_time            double comment '租赁年限',
   rent_pay             double comment '租赁租金',
   control_room_area    double comment '中控室建筑面积',
   sell_sum				double comment '出让金额',
   ground_condition     int comment '土地平整情况（1平地、2山坡、3水面）',
   has_shelter          varchar(8) comment '附近有无遮挡',
   has_pollute          varchar(8) comment '有无污染源',
   transformer_capacity double comment '上级变压器容量',
   voltage_level    	varchar(50) comment '并网电压等级',
   electricity_distance double comment '电网接入点距离',
   measure_point        int comment '计量点（1站内、2变电站）',
   plan_build_volume    double comment '拟建设容量',
   project_holder_type  int comment '项目支架类型（1地面固定式、2单轴、3双轴）',
   ground_project_type  int comment '项目类型（1地面、2农光互补、3鱼光互补）',
   cooperation_type     varchar(30) comment '与能融网合作方式（1EPC、2申请融资、3推介项目、4转让）',
   plan_financing       double comment '拟融资金额',
   financing_type       int comment '融资方式（1融资租赁（直租）、2融资租赁（回租）、3股权融资）',
   company_invest       double comment '单位投资',
   company_EPC          varchar(100) comment 'EPC厂家',
   capacity_level       varchar(50) comment '资质等级',
   company_component    varchar(100) comment '组件厂家',
   component_type       varchar(100) comment '组件规格型号及数量',
   company_inverter     varchar(100) comment '逆变器厂家',
   inverter_type        varchar(100) comment '逆变器规格型号及数量',
   synchronize_date     date comment '并网时间（date）',
   electricity_data     varchar(20) comment '历史发电量数据（最近一年）（附件URL）',
   project_backup       varchar(20) comment '项目备案（附件URL）',
   electricity_backup   varchar(20) comment '电网接入备案（附件URL）',
   ground_rent_agreement varchar(20) comment '土地租赁协议（附件URL）',
   rent_time2           double comment '租赁年限',
   rent_pay2            double comment '租赁租金',
   ground_opinion       varchar(20) comment '土地预审意见（附件URL）',
   project_proposal     varchar(20) comment '项目建议书（附件URL）',
   project_report       varchar(20) comment '项目可研报告（附件URL）',
   environment_assessment varchar(20) comment '环评（附件URL）',
   land_certificate     varchar(20) comment '土地证（附件URL）',
   electricity_price_reply varchar(20) comment '物价局电价批复（附件URL）',
   is_old_project       varchar(20) comment '是否进入当年省光伏项目目录（附件URL）',
   completion_report    varchar(20) comment '竣工验收报告（附件URL）',
   completion_paper     varchar(20) comment '竣工图纸（附件URL）',
   history_data         varchar(20) comment '历史发电量数据（附件URL）',
   electricity_bill     varchar(20) comment '电费结算票据（附件URL）',
   comment              varchar(500) comment '备注',
   create_date          datetime comment '创建时间',
   change_date          datetime comment '修改时间',
   status               int not null default 0 comment '状态类型：0正常、1已激活、2未激活、11未提交、12已提交未查看（业务员界面高亮处理）、13已提交已查看、21签意向合同（可以推送）、22签意向合同、31签融资合同、41已推送、42未推送、51尽职调查已保存、52尽职调查已提交、9999删除',
   primary key (id),
   INDEX `ground_project_id` (`project_id`)
)ENGINE = InnoDB DEFAULT CHARSET = utf8;

alter table ENF_Ground comment '大型地面电站/地面分布式';

/*==============================================================*/
/* Table: ENF_Housetop                                          */
/*==============================================================*/
create table ENF_Housetop
(
   id                   bigint not null auto_increment,
   project_id           bigint not null comment '归属项目id',
   picture1             varchar(100) comment '图片1URL',
   picture2             varchar(100) comment '图片2URL',
   picture3             varchar(100) comment '图片3URL',
   picture4             varchar(100) comment '图片4URL',
   picture5             varchar(100) comment '图片5URL',
   picture6             varchar(100) comment '图片6URL',
   picture7             varchar(100) comment '图片7URL',
   picture8             varchar(100) comment '图片8URL',
   picture9             varchar(100) comment '图片9URL',
   picture10            varchar(100) comment '图片10URL',
   picture11            varchar(100) comment '图片11URL',
   picture12            varchar(100) comment '图片12URL',
   contract             varchar(20) comment '合同的docID',
   housetop_owner       varchar(50) comment '屋顶业主名称',
   company_type         int comment '企业类型（1国企（上市公司）、2外企（上市公司）、3私企（上市公司）、4国企（非上市公司）、5外企（非上市公司）、6私企（非上市公司））',
   company_capital      double comment '注册资本金',
   housetop_property_prove varchar(20) comment '屋顶产权证明（附件ID）',
   electricity_total    double comment '年用电量',
   electricity_pay      double comment '电费',
   electricity_pay_list varchar(20) comment '最近一年电费明细（附件ID）',
   housetop_type        int comment '屋顶类型（0其他、1混凝土、2彩钢瓦）',
   housetop_type_other  varchar(20) comment '其他房屋类型信息',
   housetop_area        double comment '屋顶面积',
   housetop_age         int comment '屋顶使用寿命',
   housetop_direction   varchar(30) comment '屋顶朝向',
   housetop_waterproof_time double comment '屋顶防水周期',
   housetop_load        double comment '屋顶活载荷',
   has_shelter          varchar(8) comment '附近有无遮挡',
   has_pollution        varchar(8) comment '有无污染源',
   transformer_capacity double comment '上级变压器容量',
   voltage_level        double comment '并网电压等级',
   synchronize_type     int comment '并网方式（1自发自用、2全额上网、3自发自用全额上网）',
   electricity_distance double comment '电网接入点距离',
   plan_build_volume    double comment '拟建设容量',
   cooperation_type     varchar(30) comment '与能融网合作方式',
   plan_financing       double comment '拟融资金额',
   financing_type       int comment '融资方式（1融资租赁（直租）、2融资租赁（回租）、3股权融资）',
   company_invest       double comment '单位投资',
   company_EPC          varchar(100) comment 'EPC厂家',
   capacity_level       varchar(50) comment '资质等级',
   company_component    varchar(100) comment '组件厂家',
   component_type       varchar(100) comment '组件规格型号及数量',
   company_inverter     varchar(100) comment '逆变器厂家',
   inverter_type        varchar(100) comment '逆变器规格型号及数量',
   synchronize_date     date comment '并网时间',
   electricity_data     varchar(20) comment '历史发电量数据（最近一年）',
   project_backup       varchar(20) comment '项目备案（附件ID）',
   electricity_backup   varchar(20) comment '电网接入备案（附件ID）',
   house_rent_agreement varchar(20) comment '屋顶租赁协议（附件ID）',
   rent_time            double comment '租赁年限',
   rent_pay             double comment '租赁租金',
   power_manage_agreement varchar(20) comment '合同能源管理协议（附件URL）',
   electricity_clear_type int comment '电价结算方式（1峰谷平电价打折、2平均电价打折、3固定电价）',
   electricity_clear    varchar(20) comment '结算电价（附件ID）',
   project_proposal     varchar(20) comment '项目建议书（附件ID）',
   project_report       varchar(20) comment '项目可研报告（附件ID）',
   housetop_load_prove  varchar(20) comment '屋顶载荷证明（附件ID）',
   completion_report    varchar(20) comment '竣工验收报告（附件ID）',
   completion_paper     varchar(20) comment '竣工图纸（附件ID）',
   history_data         varchar(20) comment '历史发电数据/辐照数据（附件ID）',
   electricity_bill     varchar(20) comment '电费结算票据（附件ID）',
   comment              varchar(500) comment '备注',
   create_date          datetime comment '创建时间',
   change_date          datetime comment '修改时间',
   status               int not null default 0 comment '状态类型：0正常、1已激活、2未激活、11未提交、12已提交未查看（业务员界面高亮处理）、13已提交已查看、21签意向合同（可以推送）、22签意向合同、31签融资合同、41已推送、42未推送、51尽职调查已保存、52尽职调查已提交、9999删除',
   primary key (id),
   INDEX `housetop_project_id` (`project_id`)
)ENGINE = InnoDB DEFAULT CHARSET = utf8;

alter table ENF_Housetop comment '屋顶分布项目表';

/*==============================================================*/
/* Table: ENF_Project                                           */
/*==============================================================*/
create table ENF_Project
(
   id                   bigint not null auto_increment,
   project_code         varchar(30) not null unique comment '项目编号',
   project_type         int comment '项目类型：1屋顶分布式、2地面分布式、3大型地面',
   project_area         varchar(10) comment '项目地区',
   project_address     	varchar(60) comment '项目详细地址',
   build_state          int comment '建设状态：1未建、2已建',
   create_date          datetime comment '创建时间',
   change_date          datetime comment '修改时间',
   status               int not null default 0 comment '状态类型：0正常、1已激活、2未激活、11未提交、12已提交未查看（业务员界面高亮处理）、13已提交已查看、21签意向合同（可以推送）、22签意向合同、31签融资合同、41已推送、42未推送、51尽职调查已保存、52尽职调查已提交、9999删除',
   primary key (id)
)ENGINE = InnoDB DEFAULT CHARSET = utf8;

/*==============================================================*/
/* Table: ENF_PushProject                                       */
/*==============================================================*/
create table ENF_PushProject
(
   id                   bigint not null auto_increment,
   email                varchar(100) comment '投资人邮箱',
   project_code         varchar(100) comment '项目编号',
   push_time            datetime comment '推送时间',
   status               int not null default 0 comment '状态类型：0正常、1已激活、2未激活、11未提交、12已提交未查看（业务员界面高亮处理）、13已提交已查看、21签意向合同（可以推送）、22签意向合同、31签融资合同、41已推送、42未推送、51尽职调查已保存、52尽职调查已提交、9999删除',
   primary key (id),
   INDEX `pushProject_email` (`email`)
)ENGINE = InnoDB DEFAULT CHARSET = utf8;

alter table ENF_PushProject comment '推送到投资方的项目表';

/*==============================================================*/
/* Table: ENF_User                                              */
/*==============================================================*/
create table ENF_User
(
   id                   bigint not null auto_increment,
   email                varchar(100) not null unique comment '注册邮箱',
   password             varchar(100) not null comment '密码',
   user_type            int not null comment '用户类型：1管理员、2业务员、3项目提供方、4投资人',
   code					varchar(100) unique comment '业务员编码',
   name					varchar(20) comment '业务员姓名',
   company_name         varchar(100) comment '企业名称',
   company_type         int comment '企业注册资本/类型：1央企国企、2中外合资、3外商独资、4大型民营、5小型民营',
   company_person       varchar(100) comment '企业法人',
   company_capital      float comment '企业注册资本',
   company_fax          varchar(20) comment '公司传真',
   company_phone        varchar(20) comment '座机',
   company_telephone    varchar(11) comment '其他手机',
   company_area         varchar(50) comment '所在地区',
   company_address      varchar(100) comment '详细地址',
   company_contacts     varchar(100) comment '联系人',
   company_contacts_phone varchar(11) comment '联系人手机',
   company_contacts_position varchar(100) comment '联系人职务',
   business_license     varchar(100) comment '公司营业执照-附件ID',
   organization_code    varchar(100) comment '组织机构代码证-附件ID',
   national_tax_certificate varchar(100) comment '国税登记证-附件ID',
   local_tax_certificate varchar(100) comment '地税登记证-附件ID',
   identity_card_front  varchar(100) comment '法人身份证正面-附件ID',
   identity_card_back   varchar(100) comment '法人身份证反面-附件ID',
   financial_audit      varchar(20) comment '财务审计报告的docID',
   create_date          datetime comment '创建时间',
   change_date          datetime comment '修改时间',
   status               int not null default 0 comment '状态类型：0正常、1已激活、2未激活、11未提交、12已提交未查看（业务员界面高亮处理）、13已提交已查看、21签意向合同（可以推送）、22签意向合同、31签融资合同、41已推送、42未推送、51尽职调查已保存、52尽职调查已提交、9999删除',
   primary key (id),
   INDEX `user_email` (`email`)
)ENGINE = InnoDB DEFAULT CHARSET = utf8;

alter table ENF_User comment '用户表';

/*==============================================================*/
/* Table: ENF_Admin                                           */
/*==============================================================*/
create table ENF_Admin
(
   id                   bigint not null auto_increment,
   user_name            varchar(100) not null unique comment '用户名',
   password             varchar(100) not null comment '密码',
   primary key (id)
)ENGINE = InnoDB DEFAULT CHARSET = utf8;

alter table ENF_Admin comment '管理员表';


/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     2015/12/2 23:31:34                           */
/*==============================================================*/
create database nengrongweb;
use nengrongweb;

drop table if exists enf_area;

drop table if exists enf_doc;

drop table if exists enf_evaluation;

drop table if exists enf_ground;

drop table if exists enf_housetop;

drop table if exists enf_project;

drop table if exists enf_pushproject;

drop table if exists enf_user;

drop table if exists enf_admin;

drop table if exists enf_component;

drop table if exists enf_inverter;

/*==============================================================*/
/* Table: enf_Area                                              */
/*==============================================================*/
create table enf_area
(
   id                   varchar(10) not null comment 'id',
   area                 varchar(50) not null comment '地区描述',
   parent_id            varchar(10) not null comment '父级id',
   primary key (id)
)ENGINE = InnoDB DEFAULT CHARSET = utf8;

alter table enf_area comment '地区表';

/*==============================================================*/
/* Table: enf_Doc                                               */
/*==============================================================*/
create table enf_doc
(
   id                   bigint not null auto_increment,
   file_name            varchar(100) not null comment '文件名称',
   file_rename          varchar(100) not null comment '文件重定向名称url',
   file_size            double not null comment '文件大小bit',
   update_date          datetime not null comment '上传时间',
   primary key (id)
)ENGINE = InnoDB DEFAULT CHARSET = utf8;

alter table enf_doc comment '附件表';

/*==============================================================*/
/* Table: enf_Evaluation                                        */
/*==============================================================*/
create table enf_evaluation
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
   doc_mul              varchar(100) comment '附件集合',
   create_date          datetime comment '创建时间',
   change_date          datetime comment '修改时间',
   status               int not null default 0 comment '状态类型：0正常、11项目未提交、12项目已提交（已提交）、13已提交意向书（已提交）、21已签意向合同（未尽职调查）、22已提交尽职调查（未签意向合同）、23已签意向合同（已尽职调查）、31已签融资合同、41已推送、42未推送、51尽职调查保存状态（尽职调查保存后项目状态）、52尽职调查提交状态、61意向书保存状态',
   delete_flag int not null default 0 comment '删除标记：0正常、9999删除',
   primary key (id),
   INDEX `evaluation_project_id` (`project_id`)
)ENGINE = InnoDB DEFAULT CHARSET = utf8;

alter table enf_evaluation comment '尽职调查表';

/*==============================================================*/
/* Table: enf_Ground                                            */
/*==============================================================*/
create table enf_ground
(
   id                   bigint not null auto_increment,
   project_id           bigint not null comment '归属项目id',
   project_intent       text comment '项目意向书',
   project_area         varchar(10) comment '项目地区',
   project_address      varchar(60) comment '项目详细地址',
   picture_full         varchar(100) comment '地面全景',
   picture_field        varchar(100) comment '场平图片',
   picture_transformer  varchar(100) comment '变电站图片',
   picture_mul          varchar(100) comment '动态图片集合', 
   contract             varchar(20) comment '合同的docID',
   project_name   varchar(50) comment '项目名称',
   project_finish_date datetime comment '项目完工时间',
   project_electricity_price double comment '项目电价',
   project_investment double comment '项目总投资',
   ground_property      int comment '土地性质（0其他、1一般农田、2林地、3荒地、4鱼塘、5基本农田）',
   ground_property_other varchar(20) comment '其土地性质信息',
   ground_area          double comment '租赁土地面积',
   rent_time            double comment '租赁年限',
   rent_pay             double comment '租赁租金',
   control_room_area    double comment '中控室建筑面积',
   sell_sum    double comment '出让金额',
   ground_condition     int comment '土地平整情况（1平地、2山坡、3水面）',
   has_shelter          varchar(8) comment '附近有无遮挡（1有、2无）',
   has_pollute          varchar(8) comment '有无污染源（1有、2无）',
   transformer_capacity double comment '上级变压器容量',
   voltage_level     varchar(50) comment '并网电压等级',
   electricity_distance double comment '电网接入点距离',
   measure_point        int comment '计量点（1站内、2变电站）',
   plan_build_volume    double comment '拟建设容量',
   electricity_data     double comment '历史发电量',
   project_holder_type  int comment '项目支架类型（1地面固定式、2单轴、3双轴）',
   ground_project_type  int comment '项目类型（1地面、2农光互补、3鱼光互补）',
   project_industry     varchar(10) comment '行业（X工业、C商业、A农业，R居民，F鱼光互补，X其他）',
   cooperation_type     varchar(30) comment '与能融网合作方式（1EPC、2申请融资、3推介项目、4转让）',
   plan_financing       double comment '拟融资金额',
   financing_type       int comment '融资方式（1融资租赁（直租）、2融资租赁（回租）、3股权融资）',
   company_invest       double comment '单位投资',
   company_EPC          varchar(100) comment 'EPC厂家',
   capacity_level       varchar(50) comment '资质等级',
   synchronize_date     date comment '并网时间（date）',
   project_backup       varchar(20) comment '项目备案（附件ID）',
   electricity_backup   varchar(20) comment '电网接入备案（附件ID）',
   ground_rent_agreement varchar(20) comment '土地租赁协议（附件ID）',
   rent_time2           double comment '租赁年限',
   rent_pay2            double comment '租赁租金',
   ground_opinion       varchar(20) comment '土地预审意见（附件ID）',
   project_proposal     varchar(20) comment '项目建议书（附件ID）',
   project_report       varchar(20) comment '项目可研报告（附件ID）',
   environment_assessment varchar(20) comment '环评（附件ID）',
   land_certificate     varchar(20) comment '土地证（附件ID）',
   electricity_price_reply varchar(20) comment '物价局电价批复（附件ID）',
   is_old_project       varchar(20) comment '是否进入当年省光伏项目目录（附件ID）',
   completion_report    varchar(20) comment '竣工验收报告（附件ID）',
   completion_paper     varchar(20) comment '竣工图纸（附件ID）',
   history_data         varchar(20) comment '历史发电量数据（附件ID）',
   electricity_bill     varchar(20) comment '电费结算票据（附件ID）',
   comment              varchar(500) comment '备注',
   create_date          datetime comment '创建时间',
   change_date          datetime comment '修改时间',
   status               int not null default 0 comment '状态类型：0正常、11项目未提交、12项目已提交（已提交）、13已提交意向书（已提交）、21已签意向合同（未尽职调查）、22已提交尽职调查（未签意向合同）、23已签意向合同（已尽职调查）、31已签融资合同、41已推送、42未推送、51尽职调查保存状态（尽职调查保存后项目状态）、52尽职调查提交状态、61意向书保存状态',
   delete_flag int not null default 0 comment '删除标记：0正常、9999删除',
   primary key (id),
   INDEX `ground_project_id` (`project_id`)
)ENGINE = InnoDB DEFAULT CHARSET = utf8;

alter table enf_ground comment '大型地面电站/地面分布式';

/*==============================================================*/
/* Table: enf_Housetop                                          */
/*==============================================================*/
create table enf_housetop
(
   id                   bigint not null auto_increment,
   project_id           bigint not null comment '归属项目id',
   project_intent       text comment '项目意向书',
   project_area         varchar(10) comment '项目地区',
   project_address      varchar(60) comment '项目详细地址',
   picture_full         varchar(100) comment '屋顶全景',
   picture_south        varchar(100) comment '屋顶正南图片',
   picture_mul          varchar(100) comment '动态图片集合', 
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
   electricity_data     double comment '历史发电量',
   housetop_age         int comment '屋顶使用寿命',
   housetop_direction   int comment '屋顶朝向（0其他，1正南，2东西向）',
   housetop_direction_other varchar(20) comment '其它屋顶朝向信息',
   housetop_waterproof_time double comment '屋顶防水周期',
   housetop_load        double comment '屋顶活载荷',
   has_shelter          varchar(8) comment '附近有无遮挡（1有、2无）',
   has_pollution        varchar(8) comment '有无污染源（1有、2无）', 
   transformer_capacity double comment '上级变压器容量',
   voltage_level        double comment '并网电压等级',
   synchronize_type     int comment '并网方式（1全部自发自用、2全额上网、3自发自用，余额上网）',
   project_industry     varchar(10) comment '行业（X工业、C商业、A农业，R居民，F鱼光互补，X其他）',
   electricity_distance double comment '电网接入点距离',
   plan_build_volume    double comment '拟建设容量',
   cooperation_type     varchar(30) comment '与能融网合作方式:1EPC、2申请融资、3推介项目、4转让',
   plan_financing       double comment '拟融资金额',
   financing_type       int comment '融资方式（1融资租赁（直租）、2融资租赁（回租）、3股权融资）',
   company_invest       double comment '单位投资',
   company_EPC          varchar(100) comment 'EPC厂家',
   capacity_level       varchar(50) comment '资质等级',
   synchronize_date     date comment '并网时间',
   project_backup       varchar(20) comment '项目备案（附件ID）',
   electricity_backup   varchar(20) comment '电网接入备案（附件ID）',
   house_rent_agreement varchar(20) comment '屋顶租赁协议（附件ID）',
   rent_time            double comment '租赁年限',
   rent_pay             double comment '租赁租金',
   power_manage_agreement varchar(20) comment '合同能源管理协议（附件ID）',
   electricity_clear_type int comment '电价结算方式（1峰谷平电价打折、2平均电价打折、3固定电价）',
   electricity_clear    varchar(20) comment '结算电价',
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
   status               int not null default 0 comment '状态类型：0正常、11项目未提交、12项目已提交（已提交）、13已提交意向书（已提交）、21已签意向合同（未尽职调查）、22已提交尽职调查（未签意向合同）、23已签意向合同（已尽职调查）、31已签融资合同、41已推送、42未推送、51尽职调查保存状态（尽职调查保存后项目状态）、52尽职调查提交状态、61意向书保存状态',
   delete_flag int not null default 0 comment '删除标记：0正常、9999删除',
   primary key (id),
   INDEX `housetop_project_id` (`project_id`)
)ENGINE = InnoDB DEFAULT CHARSET = utf8;

alter table enf_housetop comment '屋顶分布项目表';

/*==============================================================*/
/* Table: enf_Project                                           */
/*==============================================================*/
create table enf_project
(
   id                   bigint not null auto_increment,
   project_code         varchar(30) not null comment '项目编号',
   project_type         int comment '项目类型：1屋顶分布式、2地面分布式、3大型地面',
   build_state          int comment '建设状态：1未建、2已建',
   provider_id   bigint comment '项目提供方id',
   highlight_flag       int default 0 comment '高亮标记，0不高亮，1高亮',
   status               int not null default 0 comment '状态类型：0正常、11项目未提交、12项目已提交（已提交）、13已提交意向书（已提交）、21已签意向合同（未尽职调查）、22已提交尽职调查（未签意向合同）、23已签意向合同（已尽职调查）、31已签融资合同、41已推送、42未推送、51尽职调查保存状态（尽职调查保存后项目状态）、52尽职调查提交状态、61意向书保存状态',
   delete_flag int not null default 0 comment '删除标记：0正常、9999删除',
   create_date          datetime comment '创建时间',
   change_date          datetime comment '修改时间',
   primary key (id)
)ENGINE = InnoDB DEFAULT CHARSET = utf8;

/*==============================================================*/
/* Table: enf_PushProject                                       */
/*==============================================================*/
create table enf_pushproject
(
   id                   bigint not null auto_increment,
   investor_id          bigint comment '投资人id',
   project_code         varchar(100) comment '项目编号',
   push_time            datetime comment '推送时间',
   highlight_flag       int default 0 comment '高亮标记，0不高亮，1高亮',
   status               int not null default 0 comment '状态类型：0正常、11项目未提交、12项目已提交（已提交）、13已提交意向书（已提交）、21已签意向合同（未尽职调查）、22已提交尽职调查（未签意向合同）、23已签意向合同（已尽职调查）、31已签融资合同、41已推送、42未推送、51尽职调查保存状态（尽职调查保存后项目状态）、52尽职调查提交状态、61意向书保存状态',
   delete_flag int not null default 0 comment '删除标记：0正常、9999删除',
   primary key (id),
   INDEX `pushproject_investor` (`investor_id`)
)ENGINE = InnoDB DEFAULT CHARSET = utf8;

alter table enf_pushproject comment '推送到投资方的项目表';

/*==============================================================*/
/* Table: enf_User                                              */
/*==============================================================*/
create table enf_user
(
   id                   bigint not null auto_increment,
   email                varchar(100) not null comment '注册邮箱',
   password             varchar(100) not null comment '密码',
   user_type            int not null comment '用户类型：1管理员、2业务员、3项目提供方、4投资人',
   code     varchar(100) comment '业务员编码',
   name     varchar(20) comment '业务员姓名',
   company_name         varchar(100) comment '企业名称',
   company_type         int comment '企业注册资本/类型：1央企国企、2中外合资、3外商独资、4大型民营、5小型民营',
   company_person       varchar(100) comment '企业法人',
   company_capital      float comment '企业注册资本',
   company_fax          varchar(20) comment '公司传真',
   company_phone        varchar(20) comment '座机',
   company_telephone    varchar(11) comment '其他手机',
   company_area         varchar(10) comment '所在地区',
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
   status               int not null default 2 comment '状态类型：1已激活、2未激活',
   delete_flag int not null default 0 comment '删除标记：0正常、9999删除',
   primary key (id),
   INDEX `user_email` (`email`)
)ENGINE = InnoDB DEFAULT CHARSET = utf8;

alter table enf_user comment '用户表';

/*==============================================================*/
/* Table: enf_Admin                                           */
/*==============================================================*/
create table enf_admin
(
   id                   bigint not null auto_increment,
   user_name            varchar(100) not null unique comment '用户名',
   password             varchar(100) not null comment '密码',
   primary key (id)
)ENGINE = InnoDB DEFAULT CHARSET = utf8;

alter table enf_admin comment '管理员表';
insert into enf_admin value(1,'admin','21232f297a57a5a743894a0e4a801fc3');/*密码admin*/

/*==============================================================*/
/* Table: enf_Component                                           */
/*==============================================================*/
create table enf_component
(
   id                   bigint not null auto_increment,
   project_id           bigint not null comment '项目id',
   component_company    varchar(100) comment '组件厂家',
   component_type       varchar(100) comment '组件规格型号',
   component_count     int comment '组件数量',
   create_date          datetime comment '创建时间',
   change_date          datetime comment '修改时间',
   delete_flag int not null default 0 comment '删除标记：0正常、9999删除',
   primary key (id),
   INDEX `component_project` (`project_id`)
)ENGINE = InnoDB DEFAULT CHARSET = utf8;

alter table enf_component comment '组件表';

/*==============================================================*/
/* Table: enf_Inverter                                           */
/*==============================================================*/
create table enf_inverter
(
   id                   bigint not null auto_increment,
   project_id           bigint not null comment '项目id',
   inverter_company     varchar(100) comment '逆变器厂家',
   inverter_type        varchar(100) comment '逆变器规格型号',
   inverter_count     int comment '逆变器数量',
   create_date          datetime comment '创建时间',
   change_date          datetime comment '修改时间',
   delete_flag int not null default 0 comment '删除标记：0正常、9999删除',
   primary key (id),
   INDEX `inverter_project` (`project_id`)
)ENGINE = InnoDB DEFAULT CHARSET = utf8;

alter table enf_inverter comment '逆变器表';


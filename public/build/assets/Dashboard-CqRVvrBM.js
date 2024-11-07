import{p as k,q as w,j as x,c as C,w as p,o as l,a as m,d as h,b as r,t as i,f as o,F as g,k as c,u as v,_ as V,s as Y,v as N}from"./app-DASpCPsI.js";import{_ as R}from"./Authenticated-D9TMJJmZ.js";import{_ as M}from"./Card-BuIJu2y1.js";import"./ApplicationLogo-BVjF6MFG.js";const $={class:"grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-5 gap-2"},D={class:""},U={class:""},B={class:"font-medium"},E={class:"font-normal"},F={key:0,class:"animate-pulse duration-700"},A={key:1,class:"overflow-auto"},P={class:"min-w-full divide-y divide-gray-400 border border-slate-400"},T={class:"py-2 px-4 border-l border-r border-slate-400 whitespace-nowrap text-xs font-bold text-center text-gray-800 uppercase bg-slate-200"},j={class:"divide-y divide-gray-400"},I={class:"py-2 px-4 text-sm font-semibold text-gray-900 whitespace-nowrap text-left bg-slate-200"},J={class:"py-2 px-2 text-sm font-semibold text-gray-900 whitespace-nowrap text-center border-l border-r border-slate-400"},L={class:"py-2 px-2 text-sm font-bold text-gray-900 whitespace-nowrap text-center border-l border-r border-slate-400"},O={class:"flex flex-col"},W={key:0,class:"flex flex-col"},z={class:"text-xs"},q={key:1,class:"flex flex-col items-center"},G={class:"grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-5 gap-2"},H={class:""},K={class:""},Q={class:""},X={class:"font-medium"},Z={class:"font-normal"},ee={key:0,class:"animate-pulse duration-700"},te={key:1,class:"overflow-auto"},re={class:"min-w-full divide-y divide-gray-400 border border-slate-400"},se={class:"py-2 px-4 border-l border-r border-slate-400 whitespace-nowrap text-xs font-bold text-center text-gray-800 uppercase bg-slate-200"},ae={class:"divide-y divide-gray-400"},le={class:"py-2 px-4 text-sm font-semibold text-gray-900 whitespace-nowrap text-left bg-slate-200"},oe={class:"py-2 px-2 text-sm font-semibold text-gray-900 whitespace-nowrap text-center border-l border-r border-slate-400"},ne={data(){return{months:[],monthly_report:{user:{searching:!1,list:{value:[],options:[]}},searching:!1,data:[],params:{date:{month:new Date().getMonth(),year:new Date().getFullYear()},sales_person:""}},yearly_report:{user:{searching:!1,list:{value:[],options:[]}},searching:!1,data:[],params:{program:"",date:new Date().getFullYear(),sales_person:""}}}},computed:{totalStudentsMonthly(){return a=>this.monthly_report.data.some(n=>n.case_status_id==a)?this.monthly_report.data.filter(n=>n.case_status_id==a).reduce((n,u)=>n+u.centre_names.reduce((s,y)=>s+(Number(y.student_numbers)||0),0),0):0},totalCentresMonthly(){return a=>{const t=this.monthly_report.data.find(e=>e.case_status_id==a);return t&&t.centre_names?t.centre_names.length:0}},listCentresWithStudentNumber(){return a=>{if(this.monthly_report.data.some(e=>e.case_status_id==a)){const e=this.monthly_report.data.filter(n=>n.case_status_id==a),d={};return e.forEach(n=>{n.centre_names.forEach(u=>{const s=u.name,y=Number(u.student_numbers)||0;d[s]||(d[s]={name:s,total_student_numbers:0}),d[s].total_student_numbers+=y})}),Object.values(d)}}},totalStudentsYearly(){return(a,t)=>{const e=this.yearly_report.data.filter(d=>d.month===a);return e.length>0?e.flatMap(u=>u.case_statuses).filter(u=>u.case_status_id===t).reduce((u,s)=>{const y=s.centre_names.reduce((f,b)=>f+(Number(b.student_numbers)||0),0);return u+y},0):0}},totalCentresYearly(){return(a,t)=>{const e=this.yearly_report.data.filter(d=>d.month===a);return e.length>0?e.flatMap(n=>n.case_statuses).filter(n=>n.case_status_id===t).reduce((n,u)=>(u.centre_names.forEach(s=>n.add(s.name)),n),new Set).size:0}}},methods:{findUserMonthly:k(function(a){a&&(this.monthly_report.user.searching=!0,w.get(route("users.find_username_email"),{params:{keyword:a}}).then(t=>{this.monthly_report.user.list.options=t.data,this.monthly_report.user.searching=!1}))},1e3),findUserYearly:k(function(a){a&&(this.yearly_report.user.searching=!0,w.get(route("users.find_username_email"),{params:{keyword:a}}).then(t=>{this.yearly_report.user.list.options=t.data,this.yearly_report.user.searching=!1}))},1e3),getMonthlyReport(){this.monthly_report.searching=!0,w.get(route("dashboard.get_monthly_report"),{params:this.monthly_report.params}).then(a=>{this.monthly_report.data=a.data,this.monthly_report.searching=!1})},getYearlyReport(){this.yearly_report.searching=!0,w.get(route("dashboard.get_yearly_report"),{params:this.yearly_report.params}).then(a=>{this.yearly_report.data=a.data,this.yearly_report.searching=!1})},getMonthName(a){return["January","February","March","April","May","June","July","August","September","October","November","December"][a-1]},getMonth(){const a=new Date().getFullYear();for(let t=1;t<=12;t++){const e={name:this.getMonthName(t),number:t,year:a};this.months.push(e)}}},mounted(){this.getMonth(),this.getMonthlyReport(),this.getYearlyReport()}},me=Object.assign(ne,{__name:"Dashboard",setup(a){return(t,e)=>{const d=x("Label"),n=x("Datepicker"),u=x("ComboBox");return l(),C(R,null,{default:p(()=>[m(M,null,{title:p(()=>e[5]||(e[5]=[h("Monthly Report")])),content:p(()=>[r("div",$,[r("div",D,[m(d,null,{default:p(()=>e[6]||(e[6]=[h("Year / Month")])),_:1}),m(n,{mode:"month",format:"MMM yyyy",modelValue:t.monthly_report.params.date,"onUpdate:modelValue":e[0]||(e[0]=s=>t.monthly_report.params.date=s),onSelect:t.getMonthlyReport,placeholder:"This Month"},null,8,["modelValue","onSelect"])]),r("div",U,[m(d,null,{default:p(()=>e[7]||(e[7]=[h("Sales Person")])),_:1}),m(u,{canClear:!0,items:t.monthly_report.user.list.options,"label-property":"display_name","value-property":"value",onSearch:t.findUserMonthly,modelValue:t.monthly_report.params.sales_person,"onUpdate:modelValue":e[1]||(e[1]=s=>t.monthly_report.params.sales_person=s),"select-placeholder":"All Records","search-placeholder":"Search sales person...",loading:t.monthly_report.user.searching,onSelect:t.getMonthlyReport},{label:p(({item:s})=>[r("span",B,[h(i(s.display_name),1),e[8]||(e[8]=r("br",null,null,-1)),r("small",E,i(s.user_email?s.user_email:"Email not available"),1)])]),_:1},8,["items","onSearch","modelValue","loading","onSelect"])])]),t.monthly_report.searching?(l(),o("div",F,e[9]||(e[9]=[r("ul",{class:"mt-5 space-y-3"},[r("li",{class:"w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"}),r("li",{class:"w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"}),r("li",{class:"w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"}),r("li",{class:"w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"}),r("li",{class:"w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"}),r("li",{class:"w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"}),r("li",{class:"w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"}),r("li",{class:"w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"}),r("li",{class:"w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"})],-1)]))):(l(),o("div",A,[r("table",P,[r("thead",null,[e[11]||(e[11]=r("tr",null,[r("th",{class:"bg-slate-200"}),r("th",{colspan:"10",class:"whitespace-nowrap py-2 px-2 text-xs font-medium text-center text-white bg-slate-800 uppercase"}," Case Status ")],-1)),r("tr",null,[e[10]||(e[10]=r("th",{class:"whitespace-nowrap py-2 px-4 text-xs font-medium text-left text-white bg-slate-800 uppercase"},"Program",-1)),(l(!0),o(g,null,c(t.$page.props.case_status,s=>(l(),o("th",T,i(s.name),1))),256))])]),r("tbody",j,[(l(!0),o(g,null,c(t.$page.props.programs,s=>(l(),o("tr",null,[r("td",I,i(s.name),1),(l(!0),o(g,null,c(t.$page.props.case_status,y=>{var f,b;return l(),o("td",J,i(((f=t.monthly_report.data.find(_=>_.program_id==s.id&&_.case_status_id==y.id))==null?void 0:f.centre_names.reduce((_,S)=>_+parseInt(S.student_numbers),0))??0)+" students / "+i(((b=t.monthly_report.data.find(_=>_.program_id==s.id&&_.case_status_id==y.id))==null?void 0:b.centre_names.length)??0)+" centres ",1)}),256))]))),256)),r("tr",null,[e[14]||(e[14]=r("td",{class:"py-2 px-4 text-sm font-bold text-gray-900 whitespace-nowrap text-left"},"Total",-1)),(l(!0),o(g,null,c(t.$page.props.case_status,s=>(l(),o("td",L,[r("div",O,[r("span",null,i(t.totalStudentsMonthly(s.id))+" students / "+i(t.totalCentresMonthly(s.id))+" centres ",1),m(v(V),null,{default:p(()=>[m(v(Y),null,{default:p(()=>e[12]||(e[12]=[r("span",{class:"text-xs underline text-slate-700"}," More Details ",-1)])),_:1}),m(v(N),null,{default:p(()=>[t.listCentresWithStudentNumber(s.id)?(l(),o("div",W,[(l(!0),o(g,null,c(t.listCentresWithStudentNumber(s.id),y=>(l(),o("span",z,i(y.name)+" / "+i(y.total_student_numbers)+" students",1))),256))])):(l(),o("div",q,e[13]||(e[13]=[r("span",{class:"text-xs"},"Data Unavailable",-1)])))]),_:2},1024)]),_:2},1024)])]))),256))])])])]))]),_:1}),m(M,null,{title:p(()=>e[15]||(e[15]=[h("Yearly Report")])),content:p(()=>[r("div",G,[r("div",H,[m(d,null,{default:p(()=>e[16]||(e[16]=[h("Program")])),_:1}),m(u,{canClear:!0,items:t.$page.props.programs,"label-property":"name","value-property":"id",modelValue:t.yearly_report.params.program,"onUpdate:modelValue":e[2]||(e[2]=s=>t.yearly_report.params.program=s),"select-placeholder":"All Programs","search-placeholder":"Search program...",onSelect:t.getYearlyReport},null,8,["items","modelValue","onSelect"])]),r("div",K,[m(d,null,{default:p(()=>e[17]||(e[17]=[h("Year")])),_:1}),m(n,{mode:"year",format:"yyyy",modelValue:t.yearly_report.params.date,"onUpdate:modelValue":e[3]||(e[3]=s=>t.yearly_report.params.date=s),onSelect:t.getYearlyReport,placeholder:"This Year"},null,8,["modelValue","onSelect"])]),r("div",Q,[m(d,null,{default:p(()=>e[18]||(e[18]=[h("Sales Person")])),_:1}),m(u,{canClear:!0,items:t.yearly_report.user.list.options,"label-property":"display_name","value-property":"value",onSearch:t.findUserYearly,modelValue:t.yearly_report.params.sales_person,"onUpdate:modelValue":e[4]||(e[4]=s=>t.yearly_report.params.sales_person=s),"select-placeholder":"All Records","search-placeholder":"Search sales person...",loading:t.yearly_report.user.searching,onSelect:t.getYearlyReport},{label:p(({item:s})=>[r("span",X,[h(i(s.display_name),1),e[19]||(e[19]=r("br",null,null,-1)),r("small",Z,i(s.user_email?s.user_email:"Email not available"),1)])]),_:1},8,["items","onSearch","modelValue","loading","onSelect"])])]),t.yearly_report.searching?(l(),o("div",ee,e[20]||(e[20]=[r("ul",{class:"mt-5 space-y-3"},[r("li",{class:"w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"}),r("li",{class:"w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"}),r("li",{class:"w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"}),r("li",{class:"w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"}),r("li",{class:"w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"}),r("li",{class:"w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"}),r("li",{class:"w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"}),r("li",{class:"w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"}),r("li",{class:"w-full h-4 bg-gray-200 rounded-full dark:bg-neutral-700"})],-1)]))):(l(),o("div",te,[r("table",re,[r("thead",null,[e[22]||(e[22]=r("tr",null,[r("th",{class:"bg-slate-200"}),r("th",{colspan:"10",class:"whitespace-nowrap py-2 px-2 text-xs font-medium text-center text-white bg-slate-800 uppercase"}," Case Status ")],-1)),r("tr",null,[e[21]||(e[21]=r("th",{class:"whitespace-nowrap py-2 px-4 text-xs font-medium text-left text-white bg-slate-800 uppercase"},"Month",-1)),(l(!0),o(g,null,c(t.$page.props.case_status,s=>(l(),o("th",se,i(s.name),1))),256))])]),r("tbody",ae,[(l(!0),o(g,null,c(t.months,s=>(l(),o("tr",null,[r("td",le,i(s.name),1),(l(!0),o(g,null,c(t.$page.props.case_status,y=>(l(),o("td",oe,i(t.totalStudentsYearly(s.number,y.id))+" students / "+i(t.totalCentresYearly(s.number,y.id))+" centres ",1))),256))]))),256))])])]))]),_:1})]),_:1})}}});export{me as default};
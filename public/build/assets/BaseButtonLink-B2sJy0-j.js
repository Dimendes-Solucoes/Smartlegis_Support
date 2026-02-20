import{m as s,l,j as c,o as i,w as u,p as d,n as f,D as p}from"./app-fd9APL3o.js";const g={__name:"BaseButtonLink",props:{href:{type:String,required:!1,default:null},color:{type:String,default:"indigo",validator:t=>["indigo","red","yellow","blue","orange"].includes(t)}},setup(t){const e=t,r=s(()=>e.href?l:"button"),n=s(()=>{const o=[`bg-${e.color}-500`,`hover:bg-${e.color}-700`,`active:bg-${e.color}-900`,`focus:border-${e.color}-900`,`focus:ring-${e.color}-300`];return e.color==="yellow"?o.push("text-black"):o.push("text-white"),o.join(" ")}),a=s(()=>`
    inline-flex items-center border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest
    focus:outline-none disabled:opacity-25 transition ease-in-out duration-150
    ${n.value}
`);return(o,m)=>(i(),c(p(r.value),{href:t.href,class:f(a.value)},{default:u(()=>[d(o.$slots,"default")]),_:3},8,["href","class"]))}};export{g as _};

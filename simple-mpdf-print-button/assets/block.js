(function (wp) {
    'use strict';
    const el = wp.element.createElement;
    wp.blocks.registerBlockType('smppb/pdf-button', {
        apiVersion: 3, title: 'PDF button', icon: 'pdf', category: 'widgets',
        attributes: {template:{type:'number',default:0},label:{type:'string',default:'Create PDF'},alignment:{type:'string',default:'left'},width:{type:'string',default:''},className:{type:'string',default:''},icon:{type:'string',default:'inherit'},output:{type:'string',default:'inherit'},size:{type:'string',default:'inherit'}},
        edit: function (props) {
            const a = props.attributes;
            return el(wp.element.Fragment, null,
                el(wp.blockEditor.InspectorControls, null,
                    el(wp.components.PanelBody, {title:'PDF button'},
                        el(wp.components.SelectControl, {label:'Template',value:a.template,options:window.smppbTemplates || [{value:0,label:'Template selector'}],onChange:v=>props.setAttributes({template:Number(v)})}),
                        el(wp.components.TextControl, {label:'Button label',value:a.label,onChange:v=>props.setAttributes({label:v})}),
                        el(wp.components.SelectControl, {label:'Alignment',value:a.alignment,options:['left','center','right'].map(v=>({value:v,label:v})),onChange:v=>props.setAttributes({alignment:v})}),
                        el(wp.components.SelectControl, {label:'Icon',value:a.icon,options:[{value:'inherit',label:'Global setting'},...['none','document','download','print'].map(v=>({value:v,label:v}))],onChange:v=>props.setAttributes({icon:v})}),
                        el(wp.components.SelectControl, {label:'PDF behaviour',value:a.output,options:[{value:'inherit',label:'Global setting'},{value:'inline',label:'Open in new tab'},{value:'download',label:'Download'}],onChange:v=>props.setAttributes({output:v})}),
                        el(wp.components.SelectControl, {label:'Size',value:a.size,options:[{value:'inherit',label:'Global setting'},...['theme','small','normal','large'].map(v=>({value:v,label:v}))],onChange:v=>props.setAttributes({size:v})}),
                        el(wp.components.TextControl, {label:'Width (e.g. 250px or 100%)',value:a.width,onChange:v=>props.setAttributes({width:v})})
                    )
                ),
                el('div', wp.blockEditor.useBlockProps({style:{textAlign:a.alignment}}),
                    el('span', {style:{display:'inline-block',padding:'12px 20px',border:'1px solid',borderRadius:'4px'}}, a.label || 'Create PDF'),
                    el('p', {style:{fontSize:'12px'}}, a.template ? 'PDF template #' + a.template : 'Template selector on website'),
                    el('p', {style:{fontSize:'12px'}}, 'Icon: '+a.icon+' · PDF: '+a.output+' · size: '+a.size+' (website appearance follows your theme and plugin settings)')
                )
            );
        },
        save: function () { return null; }
    });
})(window.wp);

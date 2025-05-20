<template>
    <editor
        :value="editorText"
        :options="editorOptions"
        :html="editorHtml"
        :visible="editorVisible"
        previewStyle="vertical"
        v-model='editorText'
        height='500px'
        initialEditType="wysiwyg"
     />
</template>

<script>
    // import 'tui-editor/dist/tui-editor.css'
    // import 'tui-editor/dist/tui-editor-contents.css'
    // import 'codemirror/lib/codemirror.css'
    import '@toast-ui/editor/dist/toastui-editor.css';
    import { Editor } from '@toast-ui/vue-editor'

    export default {
        name: 'TextEditor',
        data(){
            return {
                editorText: '',
                editorOptions:{
                    hideModeSwitch: true,
                    minHeight: '300px',
                    language: 'en_US',
                    useCommandShortcut: true,
                    usageStatistics: true,
                    toolbarItems:[
                        ['heading', 'bold', 'italic', 'strike'],
                        ['hr', 'quote'],
                        ['ul', 'ol', 'task', 'indent', 'outdent'],
                        ['table', 'image', 'link'],
                        ['code', 'codeblock']
                    ]
                },
                editorHtml:'',
                editorVisible: true
            }
        },
        components:{
            'editor': Editor
        },
        methods: {
            getHTML() {
                let html = this.$refs.toastuiEditor.invoke('getHTML');
            }
        },
    }
</script>

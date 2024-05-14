import Api from "./api.js";

document.addEventListener("DOMContentLoaded", async () => {
  const app = new Note();
  app.initEditor();
});

class Note {
  constructor() {
    this.pageId = window.location.href.split("/note/").pop();
    console.log(this.pageId);
  }

  async fetchCurrentPage() {
    return (await Api.get(`/note/${this.pageId}`)).content;
  }

  async addNote() {}

  initEditor() {
    this.editor = new EditorJS({
      data: {
        blocks: this.content,
      },
      holder: "editor",
      tools: {
        header: Header,
        list: List,
      },
    });
  }
}

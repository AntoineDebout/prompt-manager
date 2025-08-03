const Ziggy = {"url":"http:\/\/prompt-manager.test","port":null,"defaults":{},"routes":{"api.prompts.test":{"uri":"api\/prompts\/test","methods":["POST"]},"prompts.index":{"uri":"\/","methods":["GET","HEAD"]},"storage.local":{"uri":"storage\/{path}","methods":["GET","HEAD"],"wheres":{"path":".*"},"parameters":["path"]}}};
if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
  Object.assign(Ziggy.routes, window.Ziggy.routes);
}
export { Ziggy };

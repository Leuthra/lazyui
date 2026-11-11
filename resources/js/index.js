/* Front-end bundle → dist/lazyui.js: the core runtime plus the tippy.js tooltip directive. */
import { boot } from './core.js';
import tooltip from './plugins/alpine/tooltip.js';

boot([tooltip]);

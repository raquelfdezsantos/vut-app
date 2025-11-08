import fs from 'node:fs/promises';
import path from 'node:path';
import ttf2woff2 from 'ttf2woff2';

const root = path.resolve('./');
const fonts = [
  'public/fonts/Source_Serif_4/SourceSerif4-VariableFont_opsz,wght.ttf',
  'public/fonts/Source_Serif_4/SourceSerif4-Italic-VariableFont_opsz,wght.ttf',
  'public/fonts/inter/Inter-VariableFont_opsz,wght.ttf',
  'public/fonts/inter/Inter-Italic-VariableFont_opsz,wght.ttf',
];

async function convert(file) {
  const abs = path.resolve(root, file);
  const out = abs.replace(/\.ttf$/i, '.woff2');
  const ttf = await fs.readFile(abs);
  const woff2 = ttf2woff2(ttf);
  await fs.writeFile(out, woff2);
  console.log(`→ ${path.relative(root, out)}`);
}

(async () => {
  try {
    console.log('Convirtiendo TTF → WOFF2...');
    for (const f of fonts) {
      try {
        await convert(f);
      } catch (e) {
        console.warn(`No se pudo convertir ${f}: ${e.message}`);
      }
    }
    console.log('Listo.');
  } catch (err) {
    console.error(err);
    process.exit(1);
  }
})();

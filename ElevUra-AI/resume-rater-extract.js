/**
 * Resume text extraction: PDF (PDF.js), DOCX (Mammoth), TXT
 */
(function (global) {
    'use strict';

    const SUPPORTED = {
        pdf: ['application/pdf', '.pdf'],
        docx: [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            '.docx'
        ],
        txt: ['text/plain', '.txt']
    };

    const STOP_EXTENSIONS = ['.doc']; // legacy Word binary — not supported

    function getExtension(name) {
        const i = (name || '').lastIndexOf('.');
        return i >= 0 ? name.slice(i).toLowerCase() : '';
    }

    function detectFileKind(file) {
        const ext = getExtension(file.name);
        if (STOP_EXTENSIONS.includes(ext)) return { kind: 'unsupported', reason: 'legacy_doc' };
        if (file.type === 'application/pdf' || ext === '.pdf') return { kind: 'pdf' };
        if (
            file.type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ||
            ext === '.docx'
        ) {
            return { kind: 'docx' };
        }
        if (file.type === 'text/plain' || ext === '.txt') return { kind: 'txt' };
        return { kind: 'unsupported', reason: 'unknown' };
    }

    function configurePdfWorker() {
        const lib = global.pdfjsLib;
        if (!lib) return false;
        if (!lib.GlobalWorkerOptions.workerSrc) {
            lib.GlobalWorkerOptions.workerSrc =
                'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        }
        return true;
    }

    /**
     * Extract text from PDF preserving line breaks where possible.
     */
    async function extractPDFText(file) {
        if (!global.pdfjsLib) {
            throw new Error('PDF.js is not loaded. Check your network connection and refresh.');
        }
        configurePdfWorker();

        const buffer = await file.arrayBuffer();
        const pdf = await global.pdfjsLib.getDocument({ data: buffer }).promise;
        const pageTexts = [];

        for (let p = 1; p <= pdf.numPages; p++) {
            const page = await pdf.getPage(p);
            const content = await page.getTextContent();
            const lines = groupTextItemsIntoLines(content.items);
            pageTexts.push(lines.join('\n'));
        }

        return {
            text: pageTexts.join('\n\n'),
            meta: { method: 'pdf.js', pageCount: pdf.numPages }
        };
    }

    /**
     * Group PDF text items by approximate Y position into lines.
     */
    function groupTextItemsIntoLines(items) {
        if (!items || !items.length) return [];

        const rows = [];
        const Y_TOLERANCE = 4;

        for (const item of items) {
            if (!item.str || !item.str.trim()) continue;
            const y = item.transform ? item.transform[5] : 0;
            const x = item.transform ? item.transform[4] : 0;
            let row = rows.find((r) => Math.abs(r.y - y) < Y_TOLERANCE);
            if (!row) {
                row = { y, parts: [] };
                rows.push(row);
            }
            row.parts.push({ x, text: item.str });
        }

        rows.sort((a, b) => b.y - a.y);

        return rows.map((row) => {
            row.parts.sort((a, b) => a.x - b.x);
            return row.parts
                .map((p) => p.text)
                .join(' ')
                .replace(/\s+/g, ' ')
                .trim();
        }).filter(Boolean);
    }

    async function extractDOCXText(file) {
        if (!global.mammoth) {
            throw new Error('Mammoth.js is not loaded. Check your network connection and refresh.');
        }
        const buffer = await file.arrayBuffer();
        const result = await global.mammoth.extractRawText({ arrayBuffer: buffer });
        return {
            text: result.value || '',
            meta: { method: 'mammoth', warnings: result.messages || [] }
        };
    }

    async function extractTXTText(file) {
        const text = await new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = (e) => resolve(e.target.result || '');
            reader.onerror = () => reject(new Error('Failed to read text file'));
            reader.readAsText(file);
        });
        return { text, meta: { method: 'plaintext' } };
    }

    async function extractResumeText(file) {
        const kind = detectFileKind(file);

        if (kind.kind === 'unsupported') {
            if (kind.reason === 'legacy_doc') {
                throw new Error(
                    'Legacy .doc files are not supported. Please save as .docx or export as PDF.'
                );
            }
            throw new Error('Unsupported file format. Please upload PDF, DOCX, or TXT.');
        }

        let result;
        switch (kind.kind) {
            case 'pdf':
                result = await extractPDFText(file);
                break;
            case 'docx':
                result = await extractDOCXText(file);
                break;
            case 'txt':
                result = await extractTXTText(file);
                break;
            default:
                throw new Error('Unsupported file format.');
        }

        if (!result.text || result.text.trim().length < 20) {
            throw new Error(
                'Could not extract enough readable text. The file may be scanned, image-only, or corrupted. Try a text-based PDF or DOCX.'
            );
        }

        return {
            rawText: result.text,
            meta: { ...result.meta, fileKind: kind.kind, fileName: file.name }
        };
    }

    function isValidResumeFile(file) {
        return detectFileKind(file).kind !== 'unsupported';
    }

    global.ResumeRaterExtract = {
        extractResumeText,
        extractPDFText,
        extractDOCXText,
        detectFileKind,
        isValidResumeFile,
        SUPPORTED
    };
})(typeof window !== 'undefined' ? window : globalThis);

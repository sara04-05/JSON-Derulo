/**
 * Resume Rater — main entry: upload, extraction, analysis orchestration
 */
(function () {
    'use strict';

    let selectedFile = null;
    const Extract = window.ResumeRaterExtract;
    const Analyzer = window.ResumeRaterAnalyzer;
    const UI = window.ResumeRaterUI;

    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const analyzeButton = document.getElementById('analyzeButton');
    const jobDescriptionEl = document.getElementById('jobDescription');

    if (!uploadArea || !Extract || !Analyzer || !UI) {
        console.error('Resume Rater: missing DOM or modules');
        return;
    }

    uploadArea.addEventListener('click', (e) => {
        if (e.target.closest('.upload-button')) return;
        fileInput.click();
    });

    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('drag-over');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('drag-over');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('drag-over');
        if (e.dataTransfer.files.length > 0) {
            handleFileSelection(e.dataTransfer.files[0]);
        }
    });

    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleFileSelection(e.target.files[0]);
        }
    });

    window.handleFileSelection = handleFileSelection;
    window.clearFile = clearFile;
    window.analyzeResume = analyzeResume;

    window.onAnalyzeAnother = function () {
        selectedFile = null;
        fileInput.value = '';
        document.getElementById('fileInfo').classList.remove('show');
        analyzeButton.disabled = true;
        uploadArea.classList.remove('file-ready');
    };

    function handleFileSelection(file) {
        if (!Extract.isValidResumeFile(file)) {
            const kind = Extract.detectFileKind(file);
            if (kind.reason === 'legacy_doc') {
                alert('Legacy .doc files are not supported. Save as .docx or PDF.');
            } else {
                alert('Please upload a PDF, DOCX, or TXT resume.');
            }
            return;
        }

        selectedFile = file;
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = (file.size / 1024).toFixed(2) + ' KB';
        document.getElementById('fileInfo').classList.add('show');
        analyzeButton.disabled = false;
        uploadArea.classList.add('file-ready');
    }

    function clearFile() {
        window.onAnalyzeAnother();
        document.getElementById('resultsSection').classList.remove('show');
    }

    async function analyzeResume() {
        if (!selectedFile) {
            alert('Please select a file first');
            return;
        }

        const uploadSection = document.querySelector('.upload-section');
        const resultsSection = document.getElementById('resultsSection');

        uploadSection.style.display = 'none';
        resultsSection.classList.add('show');
        UI.showLoading('Extracting and analyzing your resume…');

        const jobDescription = jobDescriptionEl ? jobDescriptionEl.value.trim() : '';

        try {
            const { rawText, meta } = await Extract.extractResumeText(selectedFile);

            await yieldToMain();
            UI.showLoading('Running ATS compatibility scan…');
            await yieldToMain();

            const analysisResult = Analyzer.analyze(rawText, jobDescription);
            analysisResult.extractionMeta = {
                ...meta,
                wordCount: rawText.split(/\s+/).filter(Boolean).length
            };

            UI.showLoading('Building your report…');
            await delay(400);

            displayResultsWithMeta(analysisResult);
        } catch (error) {
            console.error('Analysis error:', error);
            UI.showError(error.message || 'Could not analyze this file. Try another format.');
        }
    }

    function displayResultsWithMeta(result) {
        UI.displayResults(result);

        const assessment = document.querySelector('.assessment-text');
        if (assessment && result.extractionMeta) {
            const m = result.extractionMeta;
            const badge = document.createElement('span');
            badge.className = 'extraction-badge';
            badge.textContent =
                'Parsed via ' +
                (m.method || m.fileKind || 'text') +
                (m.pageCount ? ' · ' + m.pageCount + ' page(s)' : '') +
                (m.wordCount ? ' · ' + m.wordCount + ' words' : '');
            assessment.after(badge);
        }
    }

    function yieldToMain() {
        return new Promise((resolve) => {
            if (typeof requestIdleCallback === 'function') {
                requestIdleCallback(() => resolve(), { timeout: 50 });
            } else {
                setTimeout(resolve, 0);
            }
        });
    }

    function delay(ms) {
        return new Promise((r) => setTimeout(r, ms));
    }
})();

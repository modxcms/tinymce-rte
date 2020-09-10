module.exports = function (grunt) {
    // Project configuration.
    grunt.initConfig({
        modx: grunt.file.readJSON('_build/config.json'),
        copy: {
            /* move files */
            tinymce: {
                files: [{
                    src: ['**/*.min.js', '**/*.gif', '**/*.png', '**/*.css'],
                    cwd: 'src/tinymce/js/tinymce/plugins/',
                    dest: 'assets/components/tinymcerte/js/vendor/tinymce/plugins/',
                    expand: true,
                    nonull: true
                }, {
                    src: ['tinymce.min.js', 'license.txt'],
                    cwd: 'src/tinymce/js/tinymce/',
                    dest: 'assets/components/tinymcerte/js/vendor/tinymce/',
                    expand: true,
                    nonull: true
                }, {
                    src: ['**/*.css', '**/*.gif', '**/tinymce*.*'],
                    cwd: 'src/tinymce/js/tinymce/skins/',
                    dest: 'assets/components/tinymcerte/js/vendor/tinymce/skins/',
                    noProcess: 'bower.json',
                    expand: true,
                    nonull: true
                }, {
                    src: '**/*.min.js',
                    cwd: 'src/tinymce/js/tinymce/themes/',
                    dest: 'assets/components/tinymcerte/js/vendor/tinymce/themes/',
                    expand: true,
                    nonull: true
                }]
            },
            skin: {
                files: [{
                    src: ['**/*'],
                    cwd: 'src/tinymce/src/skins/lightgray/',
                    dest: 'src/tinymce/src/skins/modx/',
                    expand: true,
                    nonull: true
                }, {
                    src: ['*.less'],
                    cwd: 'src/modx',
                    dest: 'src/tinymce/src/skins/modx/main/less/desktop/',
                    expand: true,
                    nonull: true
                }]
            }
        },
        gitclone: {
            tinymce: {
                options: {
                    repository: 'https://github.com/tinymce/tinymce.git',
                    branch: '4.x',
                    directory: 'src/tinymce'
                }
            }
        },
        shell: {
            installgrunt: {
                command: [
                    'npm i -g yarn grunt-cli',
                    'yarn',
                ].join('&&'),
                cwd: 'src/tinymce'
            },
            buildjs: {
                command: [
                    'grunt'
                ].join('&&'),
                cwd: 'src/tinymce'
            },
            buildcss: {
                command: [
                    'grunt less'
                ].join('&&'),
                cwd: 'src/tinymce'
            }
        },
        'regex-replace': {
            modx: {
                src: ['src/tinymce/Gruntfile.js'],
                actions: [
                    {
                        name: 'modx',
                        search: '([ ]*)(\'js/tinymce/skins/)(lightgray)(/.*?.css\': \'src/skins/)(\\3)(/main/less/.*?.less\')\\n',
                        replace: '$1$2$3$4$5$6,\n$1$2modx$4modx$6\n',
                        flags: 'g'
                    }
                ]
            }
        },
        curl: {
            i18n: {
                src: {
                    url: 'https://www.tiny.cloud/tinymce-services-azure/1/i18n/download?langs=ar,be,bg_BG,cs,da,de,el,es,et,fa,fi,fr_FR,he_IL,id,it,ja,nl,pl,pt_BR,ro,ru,sk,sv_SE,th_TH,uk,zh_CN',
                    method: 'GET'
                },
                dest: 'node_modules/tinymce/langs/tinymce_languages.zip'
            }
        },
        unzip: {
            i18n: {
                src: 'node_modules/tinymce/langs/tinymce_languages.zip',
                dest: 'assets/components/tinymcerte/js/vendor/tinymce/'
            }
        },
        watch: {
            config: {
                files: [
                    '_build/config.json'
                ],
                tasks: ['default']
            }
        },
        bump: {
            version: {
                files: [{
                    src: 'core/components/tinymcerte/model/tinymcerte/tinymcerte.class.php',
                    dest: 'core/components/tinymcerte/model/tinymcerte/tinymcerte.class.php'
                }],
                options: {
                    replacements: [{
                        pattern: /version = '\d+.\d+.\d+[-a-z0-9]*'/ig,
                        replacement: 'version = \'' + '<%= modx.version %>' + '\''
                    }]
                }
            }
        }
    });

    //load the packages
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-curl');
    grunt.loadNpmTasks('grunt-git');
    grunt.loadNpmTasks('grunt-regex-replace');
    grunt.loadNpmTasks('grunt-shell');
    grunt.loadNpmTasks('grunt-string-replace');
    grunt.loadNpmTasks('grunt-zip');
    grunt.renameTask('string-replace', 'bump');

    //register the task
    grunt.registerTask('prepare', ['gitclone', 'i18n', 'shell:installgrunt', 'regex-replace']);
    grunt.registerTask('buildjs', ['shell:buildjs', 'copy:tinymce']);
    grunt.registerTask('buildcss', ['copy:skin', 'shell:buildcss', 'copy:tinymce']);
    grunt.registerTask('build', ['buildjs', 'buildcss']);
    grunt.registerTask('i18n', ['curl', 'unzip']);
    grunt.registerTask('default', ['bump']);
};

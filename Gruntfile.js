module.exports = function (grunt) {
    // Project configuration.
    grunt.initConfig({
        modx: grunt.file.readJSON('_build/config.json'),
        tinymce_version: '5.7.0',
        banner: '/*!\n' +
            ' * <%= modx.name %> - <%= modx.description %>\n' +
            ' * Version: <%= modx.version %>\n' +
            ' * Build date: <%= grunt.template.today("yyyy-mm-dd") %>\n' +
            ' */\n',
        copy: {
            tinymce: {
                files: [{
                    src: ['**/*.min.js'],
                    cwd: 'src/tinymce/js/tinymce/icons/',
                    dest: 'assets/components/tinymcerte/js/vendor/tinymce/icons/',
                    expand: true,
                    nonull: true
                }, {
                    src: ['**/*.min.js'],
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
                    src: ['**/*.min.css', '**/*.woff'],
                    cwd: 'src/tinymce/js/tinymce/skins/',
                    dest: 'assets/components/tinymcerte/js/vendor/tinymce/skins/',
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
            tinymce_debug: {
                files: [{
                    src: ['**/*.js', '!**/*.min.js'],
                    cwd: 'src/tinymce/js/tinymce/icons/',
                    dest: 'assets/components/tinymcerte/js/vendor/tinymce/icons/',
                    expand: true,
                    nonull: true,
                    ext: '.min.js'
                }, {
                    src: ['**/*.js', '!**/*.min.js'],
                    cwd: 'src/tinymce/js/tinymce/plugins/',
                    dest: 'assets/components/tinymcerte/js/vendor/tinymce/plugins/',
                    expand: true,
                    nonull: true,
                    ext: '.min.js'
                }, {
                    src: ['tinymce.min.js', 'license.txt'],
                    cwd: 'src/tinymce/js/tinymce/',
                    dest: 'assets/components/tinymcerte/js/vendor/tinymce/',
                    expand: true,
                    nonull: true
                }, {
                    src: ['**/*.min.css', '**/*.woff'],
                    cwd: 'src/tinymce/js/tinymce/skins/',
                    dest: 'assets/components/tinymcerte/js/vendor/tinymce/skins/',
                    expand: true,
                    nonull: true
                }, {
                    src: ['**/*.js', '!**/*.min.js'],
                    cwd: 'src/tinymce/js/tinymce/themes/',
                    dest: 'assets/components/tinymcerte/js/vendor/tinymce/themes/',
                    expand: true,
                    nonull: true,
                    ext: '.min.js'
                }]
            },
            modx: {
                files: [{
                    src: ['**/plugin.js'],
                    cwd: 'src/modx/plugins/',
                    dest: 'assets/components/tinymcerte/js/vendor/tinymce/plugins/',
                    expand: true,
                    nonull: true,
                    ext: '.min.js'
                }]
            },
            skin: {
                files: [{
                    src: ['**/*.min.css', '**/*.woff'],
                    cwd: 'src/modx/skins/',
                    dest: 'assets/components/tinymcerte/js/vendor/tinymce/skins/',
                    expand: true,
                    nonull: true
                }]
            }
        },
        uglify: {
            mgr: {
                options: {
                    banner: '<%= banner %>'
                },
                files: [{
                    src: ['*.js'],
                    cwd: 'src/modx/mgr/',
                    dest: 'assets/components/tinymcerte/js/mgr/',
                    expand: true,
                    nonull: true,
                    ext: '.min.js'
                }]
            },
            plugins: {
                options: {
                    banner: '<%= banner %>'
                },
                files: [{
                    src: ['**/*.js', '!**/*.original.js'],
                    cwd: 'src/modx/plugins/',
                    dest: 'assets/components/tinymcerte/js/vendor/tinymce/plugins/',
                    expand: true,
                    nonull: true,
                    ext: '.min.js'
                }]
            }
        },
        clean: {
            tinymce: {
                src: ['src/tinymce']
            }
        },
        curl: {
            tinymce: {
                src: {
                    url: 'https://download.tiny.cloud/tinymce/community/tinymce_<%= tinymce_version %>_dev.zip',
                    method: 'GET'
                },
                dest: 'src/tinymce_dev.zip'
            },
            i18n: {
                src: {
                    url: 'https://www.tiny.cloud/tinymce-services-azure/1/i18n/download?langs=ar,be,bg_BG,cs,da,de,el,es,et,fa,fi,fr_FR,he_IL,id,it,ja,nl,pl,pt_BR,ro,ru,sk,sv_SE,th_TH,uk,zh_CN',
                    method: 'GET'
                },
                dest: 'src/tinymce_languages.zip'
            }
        },
        unzip: {
            tinymce: {
                src: 'src/tinymce_dev.zip',
                dest: 'src/'
            },
            i18n: {
                src: 'src/tinymce_languages.zip',
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
                        replacement: 'version = \'<%= modx.version %>\''
                    }]
                }
            }
        }
    });

    //load the packages
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-curl');
    grunt.loadNpmTasks('grunt-string-replace');
    grunt.loadNpmTasks('grunt-zip');
    grunt.renameTask('string-replace', 'bump');

    //register the task
    grunt.registerTask('prepare', ['curl', 'unzip', 'copy:tinymce', 'uglify', 'copy:skin']);
    grunt.registerTask('prepare_debug', ['curl', 'unzip', 'copy:tinymce_debug', 'copy:modx', 'copy:skin']);
    grunt.registerTask('update', ['clean', 'prepare']);
    grunt.registerTask('default', ['bump']);
};

@extends('layouts.admin')

@section('content')
<div id="no-code-builder" class="builder-layout-elite" :class="{'left-open': activeSidebar === 'left', 'right-open': activeSidebar === 'right'}">

    <div class="mobile-builder-nav d-xl-none">
        <button @click="toggleSidebar('left')" :class="{'active': activeSidebar === 'left'}" class="nav-icon">
            <i class="fas fa-layer-group"></i>
        </button>
        <span class="builder-brand">SITE BUILDER</span>
        <button @click="toggleSidebar('right')" :class="{'active': activeSidebar === 'right'}" class="nav-icon">
            <i class="fas fa-cog"></i>
        </button>
    </div>


    <div class="column-library d-flex flex-column" :class="{'mobile-active': activeSidebar === 'left'}">
        <div class="glass-card rounded-2xl p-6 mb-4">
            <div class="d-flex align-center gap-3 mb-6">
                <div class="stat-icon-box bg-primary-soft w-48 h-48">
                    <i class="fas fa-layer-group fs-sm"></i>
                </div>
                <h3 class="fs-md font-800 m-0 text-white">@{{ translations.page_structure }}</h3>
            </div>
            
            <button @click="openLibraryModal" class="btn-gradient w-full py-3 d-flex align-center justify-center gap-2 font-700 rounded-xl hover-scale">
                <i class="fas fa-plus-circle"></i> @{{ translations.add_section }}
            </button>
        </div>

        <div class="glass-card rounded-2xl p-6 flex-1">
            <div v-if="activeSections.length === 0" class="text-center py-12">
                <i class="fas fa-layer-group fs-3xl mb-4 opacity-30"></i>
                <p class="text-muted fs-sm">@{{ translations.no_sections_added }}</p>
            </div>
            
            <draggable v-model="activeSections" @start="onDragStart" @end="onDragEnd" item-key="id" class="no-list-style">
                <template #item="{element, index}">
                    <div @click="selectSection(index)" 
                         :class="{'bg-purple-soft border-primary': activeIndex === index}" 
                         class="glass-card-compact p-4 mb-3 pointer hover-glow d-flex align-center gap-3 position-relative transition-all rounded-xl">
                        <i class="fas fa-grip-vertical opacity-30 fs-xs"></i>
                        <div class="flex-1 min-w-0">
                            <div class="font-800 truncate fs-sm text-white">@{{ element.name }}</div>
                        </div>
                        <i @click.stop="removeSection(index)" class="fas fa-times pointer opacity-50 hover-danger fs-sm transition-all"></i>
                    </div>
                </template>
            </draggable>
        </div>
    </div>


    <div @click="activeSidebar = null" class="column-preview d-flex flex-column">

        <div class="glass-card rounded-2xl p-4 m-4">
            <div class="d-flex justify-between align-center flex-wrap gap-4">

                <div class="glass-card-compact p-2 d-flex align-center gap-1 rounded-lg">
                    <button @click="previewSize = 'mobile'" 
                            :class="{'ds-tab-btn-xs active': previewSize === 'mobile', 'ds-tab-btn-xs': previewSize !== 'mobile'}" 
                            class="px-3 py-2">
                        <i class="fas fa-mobile-alt me-1"></i> @{{ translations.mobile }}
                    </button>
                    <button @click="previewSize = 'tablet'" 
                            :class="{'ds-tab-btn-xs active': previewSize === 'tablet', 'ds-tab-btn-xs': previewSize !== 'tablet'}" 
                            class="px-3 py-2">
                        <i class="fas fa-tablet-alt me-1"></i> @{{ translations.tablet }}
                    </button>
                    <button @click="previewSize = 'desktop'" 
                            :class="{'ds-tab-btn-xs active': previewSize === 'desktop', 'ds-tab-btn-xs': previewSize !== 'desktop'}" 
                            class="px-3 py-2">
                        <i class="fas fa-desktop me-1"></i> @{{ translations.desktop }}
                    </button>
                </div>


                <div class="d-flex align-center gap-3">
                    <button @click="refreshPreview" class="glass-card-compact p-3 pointer hover-blight rounded-lg">
                        <i class="fas fa-sync-alt text-white"></i>
                    </button>
                    <button @click="exportPage" class="btn-gradient py-2 px-4 d-flex align-center gap-2 rounded-xl">
                        <i class="fas fa-file-archive"></i> @{{ translations.export_to_zip }}
                    </button>
                </div>
            </div>
        </div>


        <div class="flex-1 p-4 d-flex align-center justify-center position-relative">

            <div v-if="activeSections.length === 0" 
                 class="empty-state-container d-flex flex-column align-center justify-center text-center p-8 z-1 fade-in">
                <div class="glass-card-compact p-8 rounded-3xl border-primary-soft shadow-2xl max-w-500 hover-scale transition-all">
                    <div class="ai-sparkle-icon mb-6">
                        <i class="fas fa-magic fs-4xl text-gradient animate-pulse"></i>
                    </div>
                    <h2 class="text-white font-900 fs-xl mb-4">@{{ translations.empty_state_title }}</h2>
                    <p class="text-muted fs-md mb-8 leading-relaxed">
                        @{{ translations.empty_state_msg }}
                    </p>
                    <div class="d-flex justify-center gap-4">
                        <button @click="openLibraryModal" class="btn-gradient px-6 py-3 rounded-xl font-800 shadow-lg">
                            <i class="fas fa-plus-circle me-2"></i> @{{ translations.add_first_section || 'Add First Section' }}
                        </button>
                    </div>
                </div>

                <div class="absolute-center bg-primary-soft blur-3xl opacity-20 w-300 h-300 rounded-full z-n1"></div>
            </div>


            <div v-show="activeSections.length > 0" 
                 :class="'size-' + previewSize" 
                 class="glass-card p-0 overflow-hidden shadow-xl position-relative transition-all rounded-2xl fade-in">
                <iframe id="preview-frame" class="w-full border-none h-full block origin-top builder-iframe-preview" 
                        :style="{ '--zoom': zoomLevel }" 
                        :src="'{{ route('lp.view', $landing_page->slug) }}?preview=1'" 
                        @load="refreshPreview"></iframe>
            </div>
        </div>


        <div class="glass-card rounded-2xl p-4 m-4">
            <div class="d-flex justify-between align-center flex-wrap gap-4">
                <div class="d-flex align-center gap-3">
                    <span class="status-dot-success"></span>
                    <span class="text-success font-800 fs-sm uppercase tracking-wider">@{{ translations.ready }}</span>
                    <a href="{{ route('lp.view', $landing_page->slug) }}" target="_blank" 
                       class="text-primary-soft hover-bright fs-sm font-700 no-underline d-flex align-center gap-1">
                        @{{ translations.view_live }} <i class="fas fa-external-link-alt fs-xs"></i>
                    </a>
                </div>
                <div class="d-flex align-center gap-3 glass-card-compact p-2 rounded-lg">
                    <i @click="adjustZoom(-0.05)" class="fas fa-minus pointer hover-bright text-muted fs-sm transition-all"></i>
                    <span class="text-white font-700 fs-sm min-w-50 text-center">@{{ Math.round(zoomLevel * 100) }}%</span>
                    <i @click="adjustZoom(0.05)" class="fas fa-plus pointer hover-bright text-muted fs-sm transition-all"></i>
                    <i @click="zoomLevel = 1.0" 
                       class="fas fa-expand-arrows-alt pointer hover-bright text-muted fs-sm transition-all"></i>
                </div>
            </div>
        </div>
    </div>


    <div class="column-settings d-flex flex-column" :class="{'mobile-active': activeSidebar === 'right'}">

        <div class="glass-card rounded-2xl p-6 mb-4">
            <div class="d-flex align-center justify-between mb-6">
                <div class="d-flex align-center gap-3">
                    <div class="stat-icon-box bg-purple-soft w-48 h-48">
                        <i class="fas fa-sliders-h fs-sm"></i>
                    </div>
                    <h3 class="fs-md font-800 m-0 text-white">@{{ activeIndex !== null ? translations.section_settings : translations.page_settings }}</h3>
                </div>

                <button v-if="activeIndex !== null" @click="activeIndex = null" class="glass-card-compact p-2 rounded-lg hover-danger transition-all">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            

            <div v-if="activeIndex === null" class="fade-in">
                <div class="glass-card-compact p-4 rounded-xl mb-4 border-primary-soft">
                    <div class="text-white font-700 fs-sm mb-2">@{{ translations.page_info || 'Page Information' }}</div>
                    <div class="text-muted fs-xs">@{{ translations.select_to_edit || 'Select any section from the list to start editing its content and style live.' }}</div>
                </div>
                
                <h4 class="fs-sm font-800 text-white mb-4 uppercase tracking-widest opacity-50">@{{ translations.page_structure }}</h4>

                <p class="text-muted fs-sm">@{{ translations.page_structure_hint || 'Use the left sidebar to manage the order of your sections.' }}</p>
            </div>
        </div>


        <div v-if="activeIndex !== null && activeSection" class="flex-1 overflow-y-auto custom-scrollbar px-1 mb-4 fade-in">
            <div class="glass-card rounded-2xl p-6">

                <div class="ds-tabs-container mb-6 sticky-top bg-dark-glass py-2">
                    <button @click="activeTab = 'content'" 
                            :class="{'ds-tab-btn active': activeTab === 'content', 'ds-tab-btn': activeTab !== 'content'}">
                        <i class="fas fa-edit me-2"></i> @{{ translations.content }}
                    </button>
                    <button @click="activeTab = 'style'" 
                            :class="{'ds-tab-btn active': activeTab === 'style', 'ds-tab-btn': activeTab !== 'style'}">
                        <i class="fas fa-palette me-2"></i> @{{ translations.styling }}
                    </button>
                </div>


                <div v-show="activeTab === 'content'" class="fade-in">
                    <div v-for="(value, key) in activeSection.content" :key="key" class="vstack gap-3 mb-6">
                        <label class="form-label-premium fs-xs font-800 uppercase tracking-widest opacity-70">@{{ key.replace(/_/g, ' ') }}</label>
                        
                        <template v-if="typeof value === 'string'">
                                                        <div v-if="key.match(/image|logo|bg|background/i)" class="vstack gap-2">
                                <div class="glass-card-compact p-3 rounded-xl d-flex align-center gap-3">
                                    <div class="w-16 h-16 rounded bg-dark d-flex align-center justify-center overflow-hidden flex-shrink-0 border-white-5">
                                        <img v-if="activeSection.content[key] && typeof activeSection.content[key] === 'string' && !activeSection.content[key].includes('fa-')" :src="activeSection.content[key]" class="w-full h-full object-cover">
                                        <i v-else class="fas fa-image opacity-30"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="d-flex gap-2 mb-2">
                                            <button @click="triggerUpload(key)" class="btn-gradient-soft fs-xs py-1 px-3 flex-1 rounded text-nowrap">
                                                <i class="fas fa-cloud-upload-alt me-2"></i> Upload
                                            </button>
                                        </div>
                                        <input type="text" v-model="activeSection.content[key]" class="input-premium fs-xxs w-full opacity-50 text-truncate" placeholder="https://...">
                                        <input type="file" :id="'file-upload-'+key" @change="handleFileUpload($event, key)" class="d-none" style="display:none" accept="image/*">
                                    </div>
                                </div>
                            </div>

                                                        <div v-else-if="key.match(/icon/i)" class="vstack gap-2">
                                <div class="glass-card-compact p-3 rounded-xl d-flex align-center gap-3">
                                    <div class="w-12 h-12 rounded-full bg-primary-soft d-flex align-center justify-center flex-shrink-0">
                                        <i :class="activeSection.content[key] || 'fas fa-icons opacity-50'" class="text-white fs-sm"></i>
                                    </div>
                                    <button @click="openIconModal(key)" class="btn-outline-white fs-xs py-2 px-3 flex-1">
                                        <i class="fas fa-icons me-2"></i> Select Icon
                                    </button>
                                </div>
                                <input type="hidden" v-model="activeSection.content[key]">
                            </div>


                            <textarea v-else-if="value.length > 50" v-model="activeSection.content[key]" 
                                      class="input-premium fs-sm" rows="4"></textarea>
                            <input v-else type="text" v-model="activeSection.content[key]" 
                                   class="input-premium fs-sm">
                        </template>
                        
                        <template v-else-if="Array.isArray(value)">
                            <div v-for="(item, index) in value" :key="index" 
                                 class="glass-card-compact p-4 mb-3 position-relative rounded-xl border-white-5">
                                <div class="d-flex justify-between align-center mb-4">
                                    <span class="fs-xs font-800 text-muted uppercase">@{{ translations.item }} @{{ index + 1 }}</span>
                                    <button @click="activeSection.content[key].splice(index, 1)" 
                                            class="badge-pill-danger fs-xxs">@{{ translations.remove }}</button>
                                </div>
                                <div class="vstack gap-4">
                                    <div v-for="(v, k) in item" :key="k" class="vstack gap-2">
                                        <label class="fs-xxs font-800 opacity-50 uppercase">@{{ k.replace(/_/g, ' ') }}</label>
                                        

                                        <div v-if="k.match(/image|logo|bg|background/i)">
                                            <div class="glass-card-compact p-2 rounded-lg d-flex align-center gap-2">
                                                <img v-if="item[k] && typeof item[k] === 'string'" :src="item[k]" class="w-10 h-10 rounded object-cover bg-dark">
                                                <img v-else src="/assets/img/placeholder.png" class="w-10 h-10 rounded object-cover bg-dark opacity-50">
                                                <div class="flex-1">
                                                    <button @click="triggerArrayUpload(key, index, k)" class="btn-gradient-soft fs-xxs py-1 px-2 w-full mb-1">
                                                        <i class="fas fa-cloud-upload-alt me-1"></i> Upload
                                                    </button>
                                                    <input type="file" :id="'file-upload-'+key+'-'+index+'-'+k" @change="handleArrayFileUpload($event, key, index, k)" class="d-none" style="display:none" accept="image/*">
                                                </div>
                                            </div>
                                            <input type="text" v-model="item[k]" class="input-premium fs-xxs w-full mt-1 opacity-50">
                                        </div>


                                        <div v-else-if="k.match(/icon/i)">
                                             <div class="d-flex gap-2">
                                                <div class="w-8 h-8 rounded bg-primary-soft d-flex align-center justify-center flex-shrink-0">
                                                    <i :class="item[k] || 'fas fa-icons opacity-50'" class="fs-xs text-white"></i>
                                                </div>
                                                <button @click="openArrayIconModal(key, index, k)" class="btn-outline-white fs-xxs flex-1 py-1">Change Icon</button>
                                             </div>
                                             <input type="hidden" v-model="item[k]">
                                        </div>

                                        <input v-else type="text" v-model="item[k]" class="input-premium fs-xs">
                                    </div>
                                </div>
                            </div>
                            <button @click="activeSection.content[key].push(Object.assign({}, value[0] || {}))" 
                                    class="btn-gradient-soft w-full py-2 fs-xs rounded-lg">
                                <i class="fas fa-plus-circle me-1"></i> @{{ translations.add }} @{{ key }}
                            </button>
                        </template>
                    </div>
                </div>


                <div v-show="activeTab === 'style'" class="fade-in">
                    <div class="grid cols-1 gap-6">
                        <div v-for="(value, key) in activeSection.style" :key="key">

                            <div v-if="key === 'padding'" class="vstack gap-3">
                                <label class="form-label-premium fs-xs font-800 uppercase tracking-widest opacity-70">@{{ formatStyleKey(key) }}</label>
                                <div class="d-flex align-center gap-4">
                                    <input type="range" v-model="activeSection.style.padding" min="0" max="200" step="10"
                                           class="flex-1 accent-primary">
                                    <span class="badge-pill-info font-800 min-w-48 text-center">@{{ activeSection.style.padding }}px</span>
                                </div>
                            </div>
                            

                            <div v-else class="vstack gap-2">
                                <div class="d-flex justify-between align-center">
                                    <label class="form-label-premium fs-xs font-800 uppercase tracking-widest opacity-70 mb-0">@{{ formatStyleKey(key) }}</label>
                                    <button @click="toggleAdvanced(key)" class="btn-action-view w-24 h-24 fs-xs opacity-50 hover-opacity-100" title="Advanced View">
                                        <i :class="advancedStyles[key] ? 'fas fa-eye-slash' : 'fas fa-code'"></i>
                                    </button>
                                </div>

                                <div class="vstack gap-3">

                                    <div class="d-flex gap-3 align-center">
                                        <div class="style-preview-box" :style="{ background: activeSection.style[key] }">
                                            <input v-if="isHexColor(activeSection.style[key])" 
                                                   type="color" v-model="activeSection.style[key]" 
                                                   class="input-color-hidden">
                                        </div>
                                        <div class="flex-1 vstack gap-1">
                                            <span class="fs-sm font-700 text-white opacity-90">@{{ getFriendlyValue(activeSection.style[key]) }}</span>
                                            <span v-if="!advancedStyles[key] && isHexColor(activeSection.style[key])" class="fs-xs font-600 opacity-50 uppercase tracking-tighter">@{{ activeSection.style[key] }}</span>
                                        </div>
                                    </div>


                                    <div v-if="!advancedStyles[key]" class="d-flex flex-wrap gap-2 py-1">
                                        <div v-for="preset in elitePresets" 
                                             :key="preset.value"
                                             @click="applyPreset(key, preset.value)"
                                             class="style-preset-btn"
                                             :style="{ background: preset.value }"
                                             :class="{ 'active': activeSection.style[key] === preset.value }"
                                             :title="preset.name">
                                        </div>
                                    </div>


                                    <div v-if="advancedStyles[key]" class="mt-1">
                                        <input type="text" v-model="activeSection.style[key]" 
                                               class="input-premium w-full fs-sm font-mono" placeholder="linear-gradient(...)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="glass-card rounded-2xl p-6 mt-auto">
            <h4 class="fs-sm font-800 text-white mb-4 d-flex align-center gap-2 uppercase tracking-widest opacity-70">
                <i class="fas fa-save text-primary"></i> @{{ translations.save_page }}
            </h4>
            
            <button @click="savePage" :disabled="isSaving" class="btn-gradient w-full py-4 mb-4 rounded-xl hover-scale shadow-lg">
                <span v-if="!isSaving" class="d-flex align-center justify-center gap-2 font-800">
                    <i class="fas fa-cloud-upload-alt"></i> @{{ translations.save_page }}
                </span>
                <span v-else class="d-flex align-center justify-center gap-2 font-800">
                    <i class="fas fa-spinner fa-spin"></i> @{{ translations.processing }}
                </span>
            </button>
            
            <div v-if="lastSaved" class="glass-card-compact p-3 rounded-lg text-center bg-success-soft border-success-soft">
                <i class="fas fa-check-circle text-success me-2"></i>
                <span class="text-success font-700 fs-xs">@{{ translations.last_saved }}: @{{ lastSaved }}</span>
            </div>
            
            <div v-if="hasUnsavedChanges" class="glass-card-compact p-3 rounded-lg text-center border-warning-soft bg-warning-soft mt-2">
                <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                <span class="text-warning font-700 fs-xs">@{{ translations.unsaved_changes }}</span>
            </div>
        </div>
    </div>


    <div v-if="showLibraryModal" class="elite-modal-overlay" @click.self="closeLibraryModal">
        <div class="elite-modal-content glass-card rounded-2xl overflow-hidden d-flex flex-column max-w-1200 max-h-90vh">

            <div class="glass-card p-6 border-bottom border-white-10">
                <div class="d-flex justify-between align-center">
                    <h3 class="fs-lg font-800 m-0 text-white d-flex align-center gap-2">
                        <i class="fas fa-layer-group text-primary"></i> 
                        <span v-if="!selectedCategory">@{{ translations.library }}</span>
                        <span v-else>@{{ (translations[selectedCategory.toLowerCase()] || selectedCategory) }} @{{ translations.variants }}</span>
                    </h3>
                    <div class="d-flex gap-2">
                        <button v-if="selectedCategory" @click="selectedCategory = null" 
                                class="badge-pill-success d-flex align-center gap-1">
                            <i class="fas fa-chevron-left"></i> @{{ translations.back }}
                        </button>
                        <button @click="closeLibraryModal" class="badge-pill-danger">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>


            <div class="flex-1 overflow-y-auto p-6 bg-dark">

                <div v-if="!selectedCategory">
                    <div class="grid cols-1 cols-2 cols-3 cols-4 gap-6">
                        <div v-for="(comps, cat) in categorizedComponents" :key="cat" 
                             @click="openCategoryModal(cat)"
                             class="glass-card pointer p-6 text-center hover-glow transition-all rounded-2xl">
                            <div class="features-icon mb-4 mx-auto glass-card-compact p-4 d-flex align-center justify-center bg-purple-soft text-primary w-100 h-100 rounded-2xl">
                                <i :class="getCategoryIcon(cat)" class="fs-2xl"></i>
                            </div>
                            <div class="text-white font-800 fs-md uppercase tracking-wider mb-2">@{{ translations[cat.toLowerCase()] || cat }}</div>
                            <div class="text-muted fs-sm">@{{ comps.length }} @{{ translations.variants }}</div>
                        </div>
                    </div>
                </div>


                <div v-else>
                    <div class="grid cols-1 cols-2 cols-3 gap-6">
                        <div v-for="comp in categorizedComponents[selectedCategory]" :key="comp.id" 
                             @click="addSection(comp); closeLibraryModal()"
                             class="variant-card glass-card p-0 overflow-hidden pointer hover-scale transition-all rounded-2xl border-white-10">

                            <div class="position-relative bg-dark-sidebar h-200 d-flex align-center justify-center p-4">
                                <div class="w-full h-full bg-contain bg-center bg-no-repeat"
                                     :style="'background-image: url(' + (comp.thumbnail_url || 'https://images.unsplash.com/photo-1557683316-973673baf926') + ')'">
                                </div>
                            </div>
                            <div class="glass-card-compact p-4 d-flex justify-between align-center border-none border-t border-primary-soft rounded-none m-0 shadow-none">
                                <div class="vstack gap-1">
                                    <div class="text-white font-900 fs-xs uppercase tracking-widest opacity-80">@{{ comp.name }}</div>
                                    <div class="text-primary-soft fs-xxs font-700 uppercase tracking-tighter">@{{ comp.type || 'Standard' }}</div>
                                </div>
                                <div class="stat-icon-box bg-purple-soft w-40 h-40 rounded-lg border border-primary-soft flex-shrink-0">
                                    <i class="fas fa-plus text-primary fs-xs"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="ds-modal-overlay" :class="{ 'active': showIconModal }" @click.self="closeIconModal">
        <div class="ds-modal-card">
            <div class="ds-modal-header border-bottom border-white-5 p-4">
                <h3 class="ds-modal-title m-0">Select Icon</h3>
                <button @click="closeIconModal" class="ds-modal-close"><i class="fas fa-times"></i></button>
            </div>
            <div class="ds-modal-body p-4">
                <input type="text" v-model="iconSearch" placeholder="Search icons (e.g. arrow, user, social)..." class="input-premium mb-4 w-full fs-sm">
                <div class="ds-icon-grid max-h-96 overflow-y-auto custom-scrollbar">
                    <button v-for="icon in filteredIcons" :key="icon" @click="selectIcon(icon)" 
                            class="ds-icon-btn"
                            :class="{'active': currentIconValue === icon}">
                        <i :class="icon"></i>
                        <span class="ds-icon-label">@{{ icon.replace('fa-solid fa-', '').replace('fa-brands fa-', '') }}</span>
                    </button>
                </div>
                <div v-if="filteredIcons.length === 0" class="text-center py-8 opacity-50">
                    <i class="fas fa-search fs-2xl mb-2"></i>
                    <p class="fs-sm">No icons found</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://unpkg.com/vue@3.4.21/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vuedraggable@4.1.0/dist/vuedraggable.umd.min.js"></script>

    <div id="builder-config" data-config="{{ json_encode($builderConfig) }}"></div>
    <script src="{{ asset('assets/js/builder_v2.js') }}"></script>
@endpush

@endsection
```

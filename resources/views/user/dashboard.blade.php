@extends('layouts.app')

@section('content')
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div class="glass-card rounded-2xl p-6 service-card">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 rounded-xl service-icon flex items-center justify-center">
                <i class="fas fa-bolt text-primary text-xl"></i>
              </div>
              <span class="text-green-400 text-sm">+24%</span>
            </div>
            <h3 class="text-2xl font-bold mb-1">12,847</h3>
            <p class="text-gray-400 text-sm">طلبات اليوم</p>
          </div>

          <div class="glass-card rounded-2xl p-6 service-card">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 rounded-xl service-icon flex items-center justify-center">
                <i class="fas fa-clock text-secondary text-xl"></i>
              </div>
              <span class="text-green-400 text-sm">-12%</span>
            </div>
            <h3 class="text-2xl font-bold mb-1">0.3s</h3>
            <p class="text-gray-400 text-sm">متوسط وقت الاستجابة</p>
          </div>

          <div class="glass-card rounded-2xl p-6 service-card">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 rounded-xl service-icon flex items-center justify-center">
                <i class="fas fa-coins text-accent text-xl"></i>
              </div>
              <span class="text-green-400 text-sm">+8%</span>
            </div>
            <h3 class="text-2xl font-bold mb-1">$2,450</h3>
            <p class="text-gray-400 text-sm">التكلفة الشهرية</p>
          </div>

          <div class="glass-card rounded-2xl p-6 service-card">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 rounded-xl service-icon flex items-center justify-center">
                <i class="fas fa-users text-pink-500 text-xl"></i>
              </div>
              <span class="text-green-400 text-sm">+156</span>
            </div>
            <h3 class="text-2xl font-bold mb-1">3,421</h3>
            <p class="text-gray-400 text-sm">المستخدمين النشطين</p>
          </div>
        </div>

        <!-- AI Services Grid -->
        <div class="mb-8">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold">خدمات الذكاء الاصطناعي</h3>
            <button class="px-4 py-2 rounded-xl bg-primary/20 text-primary text-sm hover:bg-primary/30 transition-all">
              عرض الكل
            </button>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <!-- Service 1: ChatGPT -->
            <div class="glass-card rounded-2xl p-6 service-card cursor-pointer group">
              <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                  <i class="fas fa-comments text-2xl text-white"></i>
                </div>
                <div class="flex-1">
                  <div class="flex items-center justify-between mb-2">
                    <h4 class="font-bold text-lg">المحادثة الذكية</h4>
                    <span class="px-2 py-1 rounded-lg bg-green-500/20 text-green-400 text-xs">متاح</span>
                  </div>
                  <p class="text-gray-400 text-sm mb-4">نموذج GPT-4 لمحادثات طبيعية وذكية</p>
                  <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                      <i class="fas fa-users"></i>
                      <span>2.4k مستخدم</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                      <i class="fas fa-star text-accent"></i>
                      <span>4.9</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="mt-4 pt-4 border-t border-gray-700/50">
                <div class="flex items-center justify-between">
                  <span class="text-xs text-gray-500">الاستخدام اليومي</span>
                  <span class="text-sm font-semibold text-primary">78%</span>
                </div>
                <div class="mt-2 h-2 bg-gray-700 rounded-full overflow-hidden">
                  <div class="h-full w-[78%] bg-gradient-to-r from-green-500 to-emerald-500 rounded-full"></div>
                </div>
              </div>
            </div>

            <!-- Service 2: Image Generation -->
            <div class="glass-card rounded-2xl p-6 service-card cursor-pointer group">
              <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                  <i class="fas fa-palette text-2xl text-white"></i>
                </div>
                <div class="flex-1">
                  <div class="flex items-center justify-between mb-2">
                    <h4 class="font-bold text-lg">توليد الصور</h4>
                    <span class="px-2 py-1 rounded-lg bg-purple-500/20 text-purple-400 text-xs">متاح</span>
                  </div>
                  <p class="text-gray-400 text-sm mb-4">DALL-E 3 لإنشاء صور احترافية</p>
                  <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                      <i class="fas fa-image"></i>
                      <span>1.8k صورة</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                      <i class="fas fa-star text-accent"></i>
                      <span>4.8</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="mt-4 pt-4 border-t border-gray-700/50">
                <div class="flex items-center justify-between">
                  <span class="text-xs text-gray-500">الاستخدام اليومي</span>
                  <span class="text-sm font-semibold text-purple-400">65%</span>
                </div>
                <div class="mt-2 h-2 bg-gray-700 rounded-full overflow-hidden">
                  <div class="h-full w-[65%] bg-gradient-to-r from-purple-500 to-pink-500 rounded-full"></div>
                </div>
              </div>
            </div>

            <!-- Service 3: Code Assistant -->
            <div class="glass-card rounded-2xl p-6 service-card cursor-pointer group">
              <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                  <i class="fas fa-code text-2xl text-white"></i>
                </div>
                <div class="flex-1">
                  <div class="flex items-center justify-between mb-2">
                    <h4 class="font-bold text-lg">مساعد البرمجة</h4>
                    <span class="px-2 py-1 rounded-lg bg-blue-500/20 text-blue-400 text-xs">متاح</span>
                  </div>
                  <p class="text-gray-400 text-sm mb-4">GitHub Copilot لكتابة الكود</p>
                  <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                      <i class="fas fa-file-code"></i>
                      <span>5.2k سطر</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                      <i class="fas fa-star text-accent"></i>
                      <span>4.9</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="mt-4 pt-4 border-t border-gray-700/50">
                <div class="flex items-center justify-between">
                  <span class="text-xs text-gray-500">الاستخدام اليومي</span>
                  <span class="text-sm font-semibold text-blue-400">92%</span>
                </div>
                <div class="mt-2 h-2 bg-gray-700 rounded-full overflow-hidden">
                  <div class="h-full w-[92%] bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full"></div>
                </div>
              </div>
            </div>

            <!-- Service 4: Voice AI -->
            <div class="glass-card rounded-2xl p-6 service-card cursor-pointer group">
              <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                  <i class="fas fa-microphone-lines text-2xl text-white"></i>
                </div>
                <div class="flex-1">
                  <div class="flex items-center justify-between mb-2">
                    <h4 class="font-bold text-lg">الصوت والكلام</h4>
                    <span class="px-2 py-1 rounded-lg bg-orange-500/20 text-orange-400 text-xs">متاح</span>
                  </div>
                  <p class="text-gray-400 text-sm mb-4">Whisper لتحويل الصوت لنص</p>
                  <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                      <i class="fas fa-headphones"></i>
                      <span>890 ساعة</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                      <i class="fas fa-star text-accent"></i>
                      <span>4.7</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="mt-4 pt-4 border-t border-gray-700/50">
                <div class="flex items-center justify-between">
                  <span class="text-xs text-gray-500">الاستخدام اليومي</span>
                  <span class="text-sm font-semibold text-orange-400">45%</span>
                </div>
                <div class="mt-2 h-2 bg-gray-700 rounded-full overflow-hidden">
                  <div class="h-full w-[45%] bg-gradient-to-r from-orange-500 to-red-500 rounded-full"></div>
                </div>
              </div>
            </div>

            <!-- Service 5: Data Analysis -->
            <div class="glass-card rounded-2xl p-6 service-card cursor-pointer group">
              <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-teal-500 to-green-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                  <i class="fas fa-chart-pie text-2xl text-white"></i>
                </div>
                <div class="flex-1">
                  <div class="flex items-center justify-between mb-2">
                    <h4 class="font-bold text-lg">تحليل البيانات</h4>
                    <span class="px-2 py-1 rounded-lg bg-teal-500/20 text-teal-400 text-xs">متاح</span>
                  </div>
                  <p class="text-gray-400 text-sm mb-4">تحليل ذكي للبيانات والتقارير</p>
                  <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                      <i class="fas fa-database"></i>
                      <span>12TB بيانات</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                      <i class="fas fa-star text-accent"></i>
                      <span>4.8</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="mt-4 pt-4 border-t border-gray-700/50">
                <div class="flex items-center justify-between">
                  <span class="text-xs text-gray-500">الاستخدام اليومي</span>
                  <span class="text-sm font-semibold text-teal-400">58%</span>
                </div>
                <div class="mt-2 h-2 bg-gray-700 rounded-full overflow-hidden">
                  <div class="h-full w-[58%] bg-gradient-to-r from-teal-500 to-green-500 rounded-full"></div>
                </div>
              </div>
            </div>

            <!-- Service 6: Translation -->
            <div class="glass-card rounded-2xl p-6 service-card cursor-pointer group">
              <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                  <i class="fas fa-language text-2xl text-white"></i>
                </div>
                <div class="flex-1">
                  <div class="flex items-center justify-between mb-2">
                    <h4 class="font-bold text-lg">الترجمة الذكية</h4>
                    <span class="px-2 py-1 rounded-lg bg-indigo-500/20 text-indigo-400 text-xs">متاح</span>
                  </div>
                  <p class="text-gray-400 text-sm mb-4">ترجمة فورية بدقة عالية</p>
                  <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                      <i class="fas fa-globe"></i>
                      <span>50+ لغة</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                      <i class="fas fa-star text-accent"></i>
                      <span>4.6</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="mt-4 pt-4 border-t border-gray-700/50">
                <div class="flex items-center justify-between">
                  <span class="text-xs text-gray-500">الاستخدام اليومي</span>
                  <span class="text-sm font-semibold text-indigo-400">37%</span>
                </div>
                <div class="mt-2 h-2 bg-gray-700 rounded-full overflow-hidden">
                  <div class="h-full w-[37%] bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Charts & Analytics Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
          <!-- Usage Chart -->
          <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-lg font-bold">استخدام الخدمات</h3>
              <select class="px-3 py-1 rounded-lg bg-card border border-gray-700 text-sm text-gray-400 focus:outline-none">
                <option>آخر 7 أيام</option>
                <option>آخر 30 يوم</option>
                <option>آخر سنة</option>
              </select>
            </div>
            <div class="flex items-end justify-between h-48 gap-2">
              <div class="flex-1 flex flex-col items-center gap-2">
                <div class="w-full bg-gradient-to-t from-primary to-secondary rounded-t-lg chart-bar" style="height: 60%;"></div>
                <span class="text-xs text-gray-500">سبت</span>
              </div>
              <div class="flex-1 flex flex-col items-center gap-2">
                <div class="w-full bg-gradient-to-t from-primary to-secondary rounded-t-lg chart-bar" style="height: 80%;"></div>
                <span class="text-xs text-gray-500">أحد</span>
              </div>
              <div class="flex-1 flex flex-col items-center gap-2">
                <div class="w-full bg-gradient-to-t from-primary to-secondary rounded-t-lg chart-bar" style="height: 45%;"></div>
                <span class="text-xs text-gray-500">اثنين</span>
              </div>
              <div class="flex-1 flex flex-col items-center gap-2">
                <div class="w-full bg-gradient-to-t from-primary to-secondary rounded-t-lg chart-bar" style="height: 90%;"></div>
                <span class="text-xs text-gray-500">ثلاثاء</span>
              </div>
              <div class="flex-1 flex flex-col items-center gap-2">
                <div class="w-full bg-gradient-to-t from-primary to-secondary rounded-t-lg chart-bar" style="height: 70%;"></div>
                <span class="text-xs text-gray-500">أربعاء</span>
              </div>
              <div class="flex-1 flex flex-col items-center gap-2">
                <div class="w-full bg-gradient-to-t from-primary to-secondary rounded-t-lg chart-bar" style="height: 85%;"></div>
                <span class="text-xs text-gray-500">خميس</span>
              </div>
              <div class="flex-1 flex flex-col items-center gap-2">
                <div class="w-full bg-gradient-to-t from-primary to-secondary rounded-t-lg chart-bar" style="height: 55%;"></div>
                <span class="text-xs text-gray-500">جمعة</span>
              </div>
            </div>
          </div>

          <!-- AI Models Status -->
          <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-lg font-bold">حالة النماذج</h3>
              <span class="text-xs text-green-400 flex items-center gap-1">
                <div class="w-2 h-2 rounded-full bg-green-400 status-dot"></div>
                جميع النماذج تعمل
              </span>
            </div>
            <div class="space-y-4">
              <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg bg-green-500/20 flex items-center justify-center">
                  <i class="fas fa-robot text-green-400"></i>
                </div>
                <div class="flex-1">
                  <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium">GPT-4 Turbo</span>
                    <span class="text-xs text-green-400">99.9% نشط</span>
                  </div>
                  <div class="h-2 bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full w-[99.9%] bg-gradient-to-r from-green-500 to-emerald-500 rounded-full"></div>
                  </div>
                </div>
              </div>

              <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center">
                  <i class="fas fa-image text-purple-400"></i>
                </div>
                <div class="flex-1">
                  <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium">DALL-E 3</span>
                    <span class="text-xs text-green-400">98.5% نشط</span>
                  </div>
                  <div class="h-2 bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full w-[98.5%] bg-gradient-to-r from-purple-500 to-pink-500 rounded-full"></div>
                  </div>
                </div>
              </div>

              <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center">
                  <i class="fas fa-code text-blue-400"></i>
                </div>
                <div class="flex-1">
                  <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium">Codex</span>
                    <span class="text-xs text-green-400">97.8% نشط</span>
                  </div>
                  <div class="h-2 bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full w-[97.8%] bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full"></div>
                  </div>
                </div>
              </div>

              <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg bg-orange-500/20 flex items-center justify-center">
                  <i class="fas fa-microphone text-orange-400"></i>
                </div>
                <div class="flex-1">
                  <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium">Whisper</span>
                    <span class="text-xs text-green-400">99.2% نشط</span>
                  </div>
                  <div class="h-2 bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full w-[99.2%] bg-gradient-to-r from-orange-500 to-red-500 rounded-full"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activity & Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Recent Activity -->
          <div class="lg:col-span-2 glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-lg font-bold">النشاط الأخير</h3>
              <button class="text-sm text-primary hover:underline">عرض الكل</button>
            </div>
            <div class="space-y-4">
              <div class="flex items-center gap-4 p-3 rounded-xl bg-white/5 hover:bg-white/10 transition-all">
                <div class="w-10 h-10 rounded-lg bg-green-500/20 flex items-center justify-center">
                  <i class="fas fa-check text-green-400"></i>
                </div>
                <div class="flex-1">
                  <p class="text-sm font-medium">تم إنشاء 5 صور جديدة</p>
                  <p class="text-xs text-gray-500">منذ 3 دقائق</p>
                </div>
                <span class="text-xs text-gray-400">DALL-E 3</span>
              </div>

              <div class="flex items-center gap-4 p-3 rounded-xl bg-white/5 hover:bg-white/10 transition-all">
                <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center">
                  <i class="fas fa-code text-blue-400"></i>
                </div>
                <div class="flex-1">
                  <p class="text-sm font-medium">تم إنشاء 150 سطر كود</p>
                  <p class="text-xs text-gray-500">منذ 15 دقيقة</p>
                </div>
                <span class="text-xs text-gray-400">Codex</span>
              </div>

              <div class="flex items-center gap-4 p-3 rounded-xl bg-white/5 hover:bg-white/10 transition-all">
                <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center">
                  <i class="fas fa-comment text-purple-400"></i>
                </div>
                <div class="flex-1">
                  <p class="text-sm font-medium">محادثة جديدة مع المساعد</p>
                  <p class="text-xs text-gray-500">منذ 30 دقيقة</p>
                </div>
                <span class="text-xs text-gray-400">GPT-4</span>
              </div>

              <div class="flex items-center gap-4 p-3 rounded-xl bg-white/5 hover:bg-white/10 transition-all">
                <div class="w-10 h-10 rounded-lg bg-orange-500/20 flex items-center justify-center">
                  <i class="fas fa-microphone text-orange-400"></i>
                </div>
                <div class="flex-1">
                  <p class="text-sm font-medium">تم تحويل 10 دقائق صوت</p>
                  <p class="text-xs text-gray-500">منذ ساعة</p>
                </div>
                <span class="text-xs text-gray-400">Whisper</span>
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="glass-card rounded-2xl p-6">
            <h3 class="text-lg font-bold mb-6">إجراءات سريعة</h3>
            <div class="space-y-3">
              <button class="w-full flex items-center gap-3 p-4 rounded-xl bg-gradient-to-r from-primary/20 to-secondary/20 border border-primary/30 hover:border-primary/50 transition-all group">
                <div class="w-10 h-10 rounded-lg bg-primary/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                  <i class="fas fa-plus text-primary"></i>
                </div>
                <span class="font-medium">محادثة جديدة</span>
              </button>

              <button class="w-full flex items-center gap-3 p-4 rounded-xl bg-gradient-to-r from-purple-500/20 to-pink-500/20 border border-purple-500/30 hover:border-purple-500/50 transition-all group">
                <div class="w-10 h-10 rounded-lg bg-purple-500/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                  <i class="fas fa-image text-purple-400"></i>
                </div>
                <span class="font-medium">توليد صورة</span>
              </button>

              <button class="w-full flex items-center gap-3 p-4 rounded-xl bg-gradient-to-r from-blue-500/20 to-cyan-500/20 border border-blue-500/30 hover:border-blue-500/50 transition-all group">
                <div class="w-10 h-10 rounded-lg bg-blue-500/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                  <i class="fas fa-file-code text-blue-400"></i>
                </div>
                <span class="font-medium">كتابة كود</span>
              </button>

              <button class="w-full flex items-center gap-3 p-4 rounded-xl bg-gradient-to-r from-orange-500/20 to-red-500/20 border border-orange-500/30 hover:border-orange-500/50 transition-all group">
                <div class="w-10 h-10 rounded-lg bg-orange-500/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                  <i class="fas fa-microphone text-orange-400"></i>
                </div>
                <span class="font-medium">تسجيل صوتي</span>
              </button>
            </div>
          </div>
        </div>
@endsection

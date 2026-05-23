<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<link rel="stylesheet" href="https://cdn.quilljs.com/1.3.7/quill.snow.css">
<style>
.editor-layout{display:grid;grid-template-columns:1fr 310px;gap:1.5rem;align-items:start;padding:1rem 0 3rem}
.ed-card{background:#fff;border-radius:10px;box-shadow:0 2px 12px rgba(0,0,0,.08);padding:1.25rem;margin-bottom:1.25rem}
.ed-card h6{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#64748b;margin:0 0 .9rem;padding-bottom:.5rem;border-bottom:1px solid #f1f5f9}
.ed-card h6 i{color:#c0392b;margin-right:.3rem}
.fg{margin-bottom:.85rem}
.fg label{display:block;font-size:.78rem;font-weight:600;color:#374151;margin-bottom:.3rem}
.fc{width:100%;padding:.5rem .7rem;border:1.5px solid #e2e8f0;border-radius:6px;font-size:.85rem;color:#1e293b;background:#f8fafc;box-sizing:border-box;transition:border-color .2s}
.fc:focus{outline:none;border-color:#c0392b;box-shadow:0 0 0 3px rgba(192,57,43,.1);background:#fff}
#title-input{font-size:1.3rem;font-weight:700;border:none;border-bottom:2px solid #e2e8f0;border-radius:0;background:transparent;padding:.4rem 0}
#title-input:focus{box-shadow:none;border-bottom-color:#c0392b;background:transparent}
.slug-bar{font-size:.72rem;color:#94a3b8;margin-top:.3rem}
.slug-bar b{color:#c0392b}
.quill-wrap{border:1.5px solid #e2e8f0;border-radius:6px;overflow:hidden}
.quill-wrap .ql-toolbar{background:#f8fafc;border:none;border-bottom:1px solid #e2e8f0}
.quill-wrap .ql-container{border:none;font-size:1rem;min-height:380px}
.wc-bar{display:flex;gap:1.5rem;font-size:.75rem;color:#64748b;padding:.45rem .75rem;background:#f8fafc;border:1px solid #e2e8f0;border-top:none;border-radius:0 0 6px 6px}
.wc-bar b{color:#1e293b}
.img-pos-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:.4rem}
.ipos{display:flex;flex-direction:column;align-items:center;gap:.2rem;padding:.4rem .2rem;border:1.5px solid #e2e8f0;border-radius:6px;cursor:pointer;font-size:.65rem;color:#64748b;background:#f8fafc;transition:all .15s}
.ipos:hover,.ipos.on{border-color:#c0392b;background:#fff5f5;color:#c0392b}
.tag-wrap{display:flex;flex-wrap:wrap;gap:.3rem;padding:.4rem;border:1.5px solid #e2e8f0;border-radius:6px;background:#f8fafc;min-height:40px;cursor:text}
.tag-pill{display:inline-flex;align-items:center;gap:.25rem;background:#fee2e2;color:#991b1b;font-size:.72rem;font-weight:600;padding:.15rem .45rem;border-radius:20px}
.tag-pill button{background:none;border:none;cursor:pointer;color:#991b1b;font-size:.85rem;line-height:1}
.tag-inp{border:none;background:none;outline:none;font-size:.8rem;min-width:80px;flex:1;color:#1e293b}
.swatches{display:flex;flex-wrap:wrap;gap:.4rem;margin-top:.5rem}
.sw{width:22px;height:22px;border-radius:50%;cursor:pointer;border:2px solid transparent;transition:transform .15s}
.sw:hover,.sw.on{transform:scale(1.25);border-color:#1e293b}
.toggle-row{display:flex;align-items:center;gap:.75rem}
.ts{position:relative;width:44px;height:24px;flex-shrink:0}
.ts input{opacity:0;width:0;height:0}
.tslider{position:absolute;inset:0;background:#cbd5e1;border-radius:24px;cursor:pointer;transition:.3s}
.tslider:before{content:'';position:absolute;height:18px;width:18px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:.3s}
.ts input:checked+.tslider{background:#c0392b}
.ts input:checked+.tslider:before{transform:translateX(20px)}
.btn-pub{width:100%;background:linear-gradient(135deg,#c0392b,#e74c3c);color:#fff;border:none;border-radius:8px;padding:.65rem;font-size:.875rem;font-weight:700;cursor:pointer;margin-bottom:.5rem;transition:opacity .2s}
.btn-pub:hover{opacity:.88}
.btn-dft{width:100%;background:#f1f5f9;color:#475569;border:none;border-radius:8px;padding:.55rem;font-size:.875rem;font-weight:600;cursor:pointer;transition:background .2s}
.btn-dft:hover{background:#e2e8f0}
@media(max-width:900px){.editor-layout{grid-template-columns:1fr}}
</style>

<div class="dashboard-layout">
<?php require 'includes/sidebar.php'; ?>

<div class="dashboard-content">
<h2 class="mb-2"><?= $article ? 'Edit Article' : 'Write New Article' ?></h2>

<!-- Single form wraps everything -->
<form id="aForm" action="/newsportal/admin/editor/save" method="POST" enctype="multipart/form-data">
<input type="hidden" name="article_id" value="<?= $article ? $article['id'] : '' ?>">
<input type="hidden" name="image_position" id="ip_val" value="<?= htmlspecialchars($article['image_position'] ?? 'center') ?>">
<input type="hidden" name="article_color" id="col_val" value="<?= htmlspecialchars($article['article_color'] ?? '#c0392b') ?>">
<input type="hidden" name="tags_hidden" id="tags_val" value="<?= htmlspecialchars(implode(',',$article_tags)) ?>">
<input type="hidden" name="slug" id="slug_val" value="<?= htmlspecialchars($article['slug'] ?? '') ?>">
<!-- Quill outputs here -->
<input type="hidden" name="content" id="content_val" value="">

<div class="editor-layout">
<!-- ===== MAIN ===== -->
<div>
    <!-- Title -->
    <div class="ed-card">
        <div class="fg" style="margin:0">
            <label style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8"><i class="fas fa-heading"></i> Headline</label>
            <input type="text" name="title" id="title-input" class="fc" placeholder="Enter a compelling headline..." value="<?= $article ? htmlspecialchars($article['title']) : '' ?>" required>
            <div class="slug-bar">URL: /article/<b id="slug_show"><?= htmlspecialchars($article['slug'] ?? 'your-article-slug') ?></b></div>
        </div>
    </div>

    <!-- Summary -->
    <div class="ed-card">
        <h6><i class="fas fa-align-left"></i> Summary / Excerpt</h6>
        <textarea name="summary" class="fc" rows="2" placeholder="Short summary shown on listing pages..."><?= $article ? htmlspecialchars($article['summary']) : '' ?></textarea>
    </div>

    <!-- Rich Content Editor -->
    <div class="ed-card">
        <h6><i class="fas fa-file-alt"></i> Article Content</h6>
        <div class="quill-wrap">
            <div id="quill-editor"><?= $article ? $article['content'] : '' ?></div>
        </div>
        <div class="wc-bar">
            <span>Words: <b id="wc">0</b></span>
            <span>Reading time: <b id="rt">~1 min</b></span>
        </div>
    </div>

    <!-- SEO -->
    <div class="ed-card">
        <h6><i class="fas fa-search"></i> SEO Meta Description</h6>
        <textarea name="meta_description" id="meta_desc" class="fc" rows="2" placeholder="Describe this article for search engines (150–160 characters recommended)..."><?= htmlspecialchars($article['meta_description'] ?? '') ?></textarea>
        <div style="font-size:.7rem;color:#94a3b8;margin-top:.25rem"><span id="mc">0</span>/160 characters</div>
    </div>
</div>

<!-- ===== SIDEBAR ===== -->
<div>
    <!-- Publish -->
    <div class="ed-card">
        <h6><i class="fas fa-paper-plane"></i> Publish</h6>
        <?php if($user && in_array($user['role'],['admin','editor'])): ?>
            <button type="submit" name="action" value="publish" class="btn-pub"><i class="fas fa-globe"></i> Publish Now</button>
            <button type="submit" name="action" value="draft" class="btn-dft"><i class="fas fa-save"></i> Save Draft</button>
        <?php else: ?>
            <button type="submit" name="action" value="pending" class="btn-pub"><i class="fas fa-paper-plane"></i> Submit for Review</button>
            <button type="submit" name="action" value="draft" class="btn-dft"><i class="fas fa-save"></i> Save Draft</button>
        <?php endif; ?>

        <div class="fg" style="margin-top:1rem;margin-bottom:0">
            <label><i class="fas fa-clock"></i> Schedule Publish (optional)</label>
            <input type="datetime-local" name="scheduled_at" class="fc" value="<?= ($article && !empty($article['scheduled_at'])) ? date('Y-m-d\TH:i', strtotime($article['scheduled_at'])) : '' ?>">
        </div>

        <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid #f1f5f9">
            <label style="font-size:.75rem;font-weight:600;color:#374151;display:block;margin-bottom:.5rem"><i class="fas fa-star" style="color:#f59e0b"></i> Featured on Homepage</label>
            <div class="toggle-row">
                <label class="ts"><input type="checkbox" name="is_featured" value="1" id="feat_chk" <?= ($article && !empty($article['is_featured'])) ? 'checked' : '' ?>><span class="tslider"></span></label>
                <span style="font-size:.8rem;color:#64748b" id="feat_lbl"><?= ($article && !empty($article['is_featured'])) ? 'Featured' : 'Not featured' ?></span>
            </div>
        </div>
    </div>

    <!-- Classification -->
    <div class="ed-card">
        <h6><i class="fas fa-tags"></i> Classification</h6>
        <div class="fg">
            <label>Category</label>
            <select name="category_id" class="fc" required>
                <option value="">Select Category...</option>
                <?php foreach($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= ($article && $article['category_id']==$cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="fg">
            <label>Article Type</label>
            <select name="article_type" class="fc">
                <option value="standard"    <?= ($article && ($article['article_type'] ?? 'standard')=='standard')   ? 'selected':'' ?>>📰 Standard</option>
                <option value="opinion"     <?= ($article && ($article['article_type'] ?? '')=='opinion')             ? 'selected':'' ?>>💬 Opinion / Editorial</option>
                <option value="interview"   <?= ($article && ($article['article_type'] ?? '')=='interview')           ? 'selected':'' ?>>🎤 Interview</option>
                <option value="photo_essay" <?= ($article && ($article['article_type'] ?? '')=='photo_essay')         ? 'selected':'' ?>>📷 Photo Essay</option>
                <option value="breaking"    <?= ($article && ($article['article_type'] ?? '')=='breaking')            ? 'selected':'' ?>>🔴 Breaking News</option>
            </select>
        </div>
        <div class="fg" style="margin:0">
            <label>Language</label>
            <select name="language" class="fc">
                <option value="en"        <?= ($article && ($article['language'] ?? 'en')=='en')         ? 'selected':'' ?>>🇬🇧 English</option>
                <option value="ne"        <?= ($article && ($article['language'] ?? '')=='ne')            ? 'selected':'' ?>>🇳🇵 Nepali (नेपाली)</option>
                <option value="bilingual" <?= ($article && ($article['language'] ?? '')=='bilingual')     ? 'selected':'' ?>>🌐 Bilingual</option>
            </select>
        </div>
    </div>

    <!-- Tags -->
    <div class="ed-card">
        <h6><i class="fas fa-hashtag"></i> Tags</h6>
        <div class="tag-wrap" id="tag_wrap">
            <?php foreach($article_tags as $t): ?>
            <span class="tag-pill" data-tag="<?= htmlspecialchars($t) ?>"><?= htmlspecialchars($t) ?><button type="button" onclick="rmTag(this)">×</button></span>
            <?php endforeach; ?>
            <input type="text" class="tag-inp" id="tag_inp" placeholder="Add tag, press Enter...">
        </div>
        <div style="font-size:.7rem;color:#94a3b8;margin-top:.35rem">Press Enter or comma to add a tag</div>
    </div>

    <!-- Featured Image -->
    <div class="ed-card">
        <h6><i class="fas fa-image"></i> Featured Image</h6>
        <div class="fg">
            <label>Upload Image</label>
            <input type="file" name="image" class="fc" accept="image/*">
            <?php if($article && $article['image_url']): ?>
            <img src="<?= htmlspecialchars($article['image_url']) ?>" style="width:100%;height:90px;object-fit:cover;border-radius:6px;margin-top:.5rem">
            <small style="color:#64748b;font-size:.7rem">Upload new to replace</small>
            <?php endif; ?>
        </div>
        <div class="fg">
            <label>Image Alignment</label>
            <div class="img-pos-grid">
                <div class="ipos <?= ($article && ($article['image_position']??'center')=='left')  ?'on':'' ?>" onclick="setPos('left',this)"><i class="fas fa-align-left"></i>Left</div>
                <div class="ipos <?= (!$article || ($article['image_position']??'center')=='center')?'on':'' ?>" onclick="setPos('center',this)"><i class="fas fa-align-center"></i>Center</div>
                <div class="ipos <?= ($article && ($article['image_position']??'center')=='right') ?'on':'' ?>" onclick="setPos('right',this)"><i class="fas fa-align-right"></i>Right</div>
                <div class="ipos <?= ($article && ($article['image_position']??'center')=='full')  ?'on':'' ?>" onclick="setPos('full',this)"><i class="fas fa-expand"></i>Full</div>
            </div>
        </div>
        <div class="fg" style="margin:0">
            <label>Image Caption</label>
            <input type="text" name="image_caption" class="fc" placeholder="Photo: Source / Agency" value="<?= $article ? htmlspecialchars($article['image_caption'] ?? '') : '' ?>">
        </div>
    </div>

    <!-- Accent Color -->
    <div class="ed-card">
        <h6><i class="fas fa-palette"></i> Accent Color</h6>
        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.5rem">
            <div id="col_prev" style="width:30px;height:30px;border-radius:50%;background:<?= htmlspecialchars($article['article_color']??'#c0392b') ?>;border:2px solid #e2e8f0"></div>
            <input type="color" id="col_pick" value="<?= htmlspecialchars($article['article_color']??'#c0392b') ?>" style="width:38px;height:30px;border:none;cursor:pointer;border-radius:4px">
            <code id="col_hex" style="font-size:.8rem;color:#64748b"><?= htmlspecialchars($article['article_color']??'#c0392b') ?></code>
        </div>
        <div class="swatches">
            <?php foreach(['#c0392b','#e74c3c','#d35400','#f39c12','#27ae60','#16a085','#2980b9','#8e44ad','#2c3e50','#1e293b','#6d28d9','#db2777'] as $sw): ?>
            <div class="sw <?= ($article && ($article['article_color']??'#c0392b')===$sw)?'on':'' ?>" style="background:<?=$sw?>" onclick="setCol('<?=$sw?>',this)" title="<?=$sw?>"></div>
            <?php endforeach; ?>
        </div>
    </div>

</div><!-- /sidebar -->
</div><!-- /editor-layout -->
</form>
</div><!-- /dashboard-content -->
</div><!-- /dashboard-layout -->

<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
// Init Quill
var quill = new Quill('#quill-editor', {
    theme: 'snow',
    placeholder: 'Write your article content here...',
    modules: {
        toolbar: [
            [{ header: [1,2,3,4,5,6,false] }],
            ['bold','italic','underline','strike'],
            [{ color:[] },{ background:[] }],
            [{ align:[] }],
            [{ list:'ordered' },{ list:'bullet' }],
            ['blockquote','code-block'],
            ['link','image'],
            ['clean']
        ]
    }
});

// Word count
function updWC() {
    var text = quill.getText().trim();
    var words = text ? text.split(/\s+/).filter(w=>w).length : 0;
    document.getElementById('wc').textContent = words.toLocaleString();
    document.getElementById('rt').textContent = '~'+Math.max(1,Math.ceil(words/200))+' min';
}
quill.on('text-change', updWC);
updWC();

// Before submit: copy Quill HTML to hidden input
document.getElementById('aForm').addEventListener('submit', function() {
    document.getElementById('content_val').value = quill.root.innerHTML;
});

// Auto slug
function slugify(t){ return t.toLowerCase().replace(/\s+/g,'-').replace(/[^\w\-]+/g,'').replace(/\-\-+/g,'-').replace(/^-+|-+$/g,''); }
document.querySelector('[name="title"]').addEventListener('input',function(){
    var s=slugify(this.value);
    document.getElementById('slug_show').textContent=s||'your-article-slug';
    document.getElementById('slug_val').value=s;
});

// Image position
function setPos(p,el){ document.getElementById('ip_val').value=p; document.querySelectorAll('.ipos').forEach(x=>x.classList.remove('on')); el.classList.add('on'); }

// Color
function setCol(h,el){
    document.getElementById('col_val').value=h;
    document.getElementById('col_prev').style.background=h;
    document.getElementById('col_hex').textContent=h;
    document.getElementById('col_pick').value=h;
    document.querySelectorAll('.sw').forEach(x=>x.classList.remove('on'));
    el.classList.add('on');
}
document.getElementById('col_pick').addEventListener('input',function(){
    document.getElementById('col_val').value=this.value;
    document.getElementById('col_prev').style.background=this.value;
    document.getElementById('col_hex').textContent=this.value;
    document.querySelectorAll('.sw').forEach(x=>x.classList.remove('on'));
});

// Tags
var tagList=<?= json_encode($article_tags) ?>;
function renderTags(){
    var wrap=document.getElementById('tag_wrap'), inp=document.getElementById('tag_inp');
    wrap.querySelectorAll('.tag-pill').forEach(p=>p.remove());
    tagList.forEach(function(t){
        var s=document.createElement('span'); s.className='tag-pill'; s.setAttribute('data-tag',t);
        s.innerHTML=t+' <button type="button" onclick="rmTag(this)">×</button>';
        wrap.insertBefore(s,inp);
    });
    document.getElementById('tags_val').value=tagList.join(',');
}
function rmTag(btn){ tagList=tagList.filter(t=>t!==btn.parentElement.getAttribute('data-tag')); renderTags(); }
document.getElementById('tag_inp').addEventListener('keydown',function(e){
    if(e.key==='Enter'||e.key===','){e.preventDefault(); var v=this.value.trim().replace(/,/g,''); if(v&&!tagList.includes(v)){tagList.push(v);renderTags();} this.value='';}
});
document.getElementById('tag_wrap').addEventListener('click',function(){ document.getElementById('tag_inp').focus(); });

// Featured label
document.getElementById('feat_chk').addEventListener('change',function(){
    document.getElementById('feat_lbl').textContent=this.checked?'Featured':'Not featured';
});

// Meta char count
var md=document.getElementById('meta_desc');
md.addEventListener('input',function(){ document.getElementById('mc').textContent=this.value.length; });
document.getElementById('mc').textContent=md.value.length;
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

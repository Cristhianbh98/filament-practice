<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Category;
use App\Models\Post;
use Faker\Core\File;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Posts';

    protected static ?string $navigationGroup = 'Blog';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make()->tabs([
                    Tab::make('Create New Post')->schema([
                        TextInput::make('title')->required(),
                        TextInput::make('slug')->required(),
                        
                        Select::make('category_id')
                        ->relationship('category', 'name')
                        ->label('Category')
                        ->searchable()
                        ->preload(),
    
                        ColorPicker::make('color')->required(),    
                    ]),
                    Tab::make('Content')->schema([
                        MarkdownEditor::make('content')->required()->columnSpanFull(),
                    ]),
                    Tab::make('Meta')->schema([
                        FileUpload::make('thumbnail')->columnSpanFull()->disk('public')->directory('thumbnail'),                        
                        TagsInput::make('tags')->required(),
                        Checkbox::make('published')->default(true),

                        Select::make('authors')
                        ->multiple()
                        ->relationship('authors', 'name')
                    ]) 
                ])->columnSpanFull(),           
                /* Section::make('Datos Generales')->Schema([                    
                    TextInput::make('title')->required(),
                    TextInput::make('slug')->required(),
                    
                    Select::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Category')
                    ->searchable(),

                    ColorPicker::make('color')->required(),

                    MarkdownEditor::make('content')->required()->columnSpanFull(),
                ])->columnSpan(2)->columns(2),
                Group::make()->schema([
                    Section::make('Thumbnail')->collapsible()->Schema([
                        FileUpload::make('thumbnail')->columnSpanFull()->disk('public')->directory('thumbnail'),                        
                    ])->columnSpan(1),
                    Section::make('Meta')->collapsible()->Schema([
                        TagsInput::make('tags')->required(),
                        Checkbox::make('published')->default(true),
                    ])->columnSpan(1),
                    Section::make('Authors')->collapsible()->Schema([
                        Select::make('authors')
                        ->multiple()
                        ->relationship('authors', 'name')
                    ])->columnSpan(1), 
                ])*/
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail'),
                TextColumn::make('title'),
                TextColumn::make('slug'),
                TextColumn::make('category.name'),
                ColorColumn::make('color'),
                TextColumn::make('tags'),
                CheckboxColumn::make('published')
            ])
            ->filters([
                Filter::make('Published Posts')->query(
                    function (Builder $query) {
                        return $query->where('published', true);
                    }
                ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
